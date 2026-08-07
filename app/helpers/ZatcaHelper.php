<?php
// app/helpers/ZatcaHelper.php

class ZatcaHelper {
    
    /**
     * توليد كود الـ QR الخاص بهيئة الزكاة والدخل (ZATCA)
     * يعتمد على تشفير TLV (Tag-Length-Value) ثم تحويله إلى Base64
     * 
     * @param string $sellerName اسم البائع (الشركة)
     * @param string $vatNumber الرقم الضريبي (15 رقم)
     * @param string $timestamp وقت وتاريخ الفاتورة (ISO 8601)
     * @param string $invoiceTotal إجمالي الفاتورة مع الضريبة
     * @param string $vatTotal إجمالي الضريبة
     * @return string كود Base64 لطباعته كـ QR
     */
    public static function generateQrCode(string $sellerName, string $vatNumber, string $timestamp, string $invoiceTotal, string $vatTotal): string {
        
        $tlv = self::toTlv(1, $sellerName) .
               self::toTlv(2, $vatNumber) .
               self::toTlv(3, $timestamp) .
               self::toTlv(4, $invoiceTotal) .
               self::toTlv(5, $vatTotal);
               
        // تحويل النتيجة النهائية إلى Base64
        return base64_encode($tlv);
    }

    /**
     * تحويل القيمة إلى صيغة TLV (الترميز، الطول، القيمة)
     */
    private static function toTlv(int $tag, string $value): string {
        // تحويل الرقم إلى حرف (Byte)
        $tagStr = chr($tag);
        
        // حساب طول السلسلة النصية بالبايت (مهم جداً للغة العربية)
        $lengthStr = chr(strlen($value));
        
        return $tagStr . $lengthStr . $value;
    }
}