<?php
// Path: app/Core/Numbering/NumberingManager.php

declare(strict_types=1);

namespace App\Core\Numbering;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Numbering Manager
 * الواجهة الموحدة التي سيتعامل معها المطورون في الكنترولرز للحصول على أرقام المستندات.
 */
class NumberingManager
{
    protected SequenceManager $sequenceManager;
    protected SequenceGenerator $generator;

    /**
     * NumberingManager constructor.
     *
     * @param SequenceManager $sequenceManager
     * @param SequenceGenerator $generator
     */
    public function __construct(SequenceManager $sequenceManager, SequenceGenerator $generator)
    {
        $this->sequenceManager = $sequenceManager;
        $this->generator = $generator;
    }

    /**
     * توليد الرقم النهائي والمُنسق لمستند معين (مثل: فاتورة، قيد، أمر شراء).
     *
     * @param string $documentType نوع المستند (sales_invoice, journal_entry, etc.)
     * @param NumberingContext $context سياق طلب الرقم (الشركة، التاريخ)
     * @return string الرقم المنسق (مثال: INV-2026-08-0005)
     * @throws BusinessException
     */
    public function generate(string $documentType, NumberingContext $context): string
    {
        // 1. جلب إعدادات التسلسل من الداتابيز
        $sequence = $this->sequenceManager->findSequence($documentType, $context);

        if (!$sequence) {
            throw new BusinessException("No active numbering sequence configured for document type: {$documentType}");
        }

        // 2. استخدام المولد الآمن لجلب الرقم القادم
        // (يقوم بحبس السجل في قاعدة البيانات حتى الانتهاء لضمان عدم التكرار)
        $nextNumber = $this->generator->getNextSequenceNumber(
            (int) $sequence->getAttribute('id'), 
            (int) $sequence->getAttribute('step') ?: 1
        );

        // 3. دمج الرقم مع النمط المطلوب (Pattern Formatting)
        $pattern = $sequence->getPatternObject();
        
        return $pattern->format($nextNumber, $context);
    }
}