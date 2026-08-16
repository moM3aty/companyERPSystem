<?php
// Path: resources/views/documents/common/notes.php
/*
 * Reusable Notes and Terms Block for Print Documents
 * Expected Variables: 
 * $notes (String)
 * $terms (String) - Optional
 */
?>
<div class="mb-8 bg-gray-50 p-4 border border-gray-200 rounded-lg text-xs text-gray-600 space-y-3 page-break-inside-avoid">
    <?php if(!empty($notes)): ?>
    <div>
        <h4 class="font-bold text-gray-800 uppercase tracking-wider mb-1">Remarks / Notes:</h4>
        <p class="whitespace-pre-line"><?= htmlspecialchars($notes) ?></p>
    </div>
    <?php endif; ?>
    
    <?php if(!empty($terms)): ?>
    <div>
        <h4 class="font-bold text-gray-800 uppercase tracking-wider mb-1 mt-2 border-t border-gray-200 pt-2">Terms & Conditions:</h4>
        <p class="whitespace-pre-line"><?= htmlspecialchars($terms) ?></p>
    </div>
    <?php endif; ?>
</div>