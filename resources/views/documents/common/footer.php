<?php
// Path: resources/views/documents/common/footer.php
/*
 * Reusable Footer for all printed documents
 */
?>
<div class="mt-12 border-t border-gray-300 pt-4 flex justify-between items-center text-[10px] text-gray-400 font-medium pb-4">
    <div>
        <p>Nour Trust ERP - System Generated Document</p>
        <p class="mt-0.5">Printed on: <?= date('Y-m-d H:i:s') ?></p>
    </div>
    <div class="text-center">
        <p>www.nourtrust.com | info@nourtrust.com</p>
    </div>
    <div class="text-right">
        <p>Page 1 of 1</p> <!-- In a dynamic PDF generator like dompdf, this is handled by CSS -->
    </div>
</div>