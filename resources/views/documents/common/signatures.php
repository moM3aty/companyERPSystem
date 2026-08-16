<?php
// Path: resources/views/documents/common/signatures.php
/*
 * Reusable Signature Block for Print Documents
 */
?>
<div class="flex justify-between items-end mt-16 pt-8 border-t border-gray-300 page-break-inside-avoid">
    <div class="text-center w-1/3 px-4">
        <div class="border-b border-gray-800 mb-2 border-dashed"></div>
        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Prepared By</p>
    </div>
    
    <div class="text-center w-1/3 px-4">
        <!-- Optional Stamp space -->
        <div class="h-16 mb-2"></div>
        <div class="border-b border-gray-800 mb-2 border-dashed"></div>
        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Authorized Signatory</p>
    </div>
    
    <div class="text-center w-1/3 px-4">
        <div class="border-b border-gray-800 mb-2 border-dashed"></div>
        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest">Received / Acknowledged By</p>
    </div>
</div>