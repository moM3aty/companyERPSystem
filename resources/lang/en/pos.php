<?php
// Path: resources/lang/en/pos.php

return [
    'module_name' => 'Point of Sale (POS)',
    
    // Terminals & Shifts
    'terminals' => 'POS Terminals',
    'shifts' => 'Cashier Shifts',
    'open_shift' => 'Open Shift',
    'close_shift' => 'Close Shift',
    'z_report' => 'End of Day (Z-Report)',
    'x_report' => 'Current Status (X-Report)',
    
    // Fields
    'terminal_name' => 'Terminal Name',
    'cashier' => 'Cashier',
    'opening_cash' => 'Opening Cash (Float)',
    'expected_cash' => 'Expected Cash in Drawer',
    'actual_cash' => 'Actual Counted Cash',
    'discrepancy' => 'Discrepancy (Shortage/Overage)',
    
    // Sales
    'scan_barcode' => 'Scan Barcode or Search...',
    'pay' => 'Pay',
    'exact_cash' => 'Exact Cash',
    'card_payment' => 'Credit / Debit Card',
    'print_receipt' => 'Print Receipt',
    'refund' => 'Process Refund',
    
    // Messages
    'shift_opened' => 'Shift opened successfully. You can now start selling.',
    'shift_closed' => 'Shift closed. Z-Report generated and GL entries posted.',
    'discrepancy_warning' => 'Warning: There is a discrepancy between expected and actual cash.',
];