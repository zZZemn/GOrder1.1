<?php
/* Change to the correct path if you copy this example! */
require 'vendor/mike42/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

/**
 * Install the printer using USB printing support, and the "Generic / Text Only" driver,
 * then share it (you can use a firewall so that it can only be seen locally).
 *
 * Use a WindowsPrintConnector with the share name to print.
 *
 * Troubleshooting: Fire up a command prompt, and ensure that (if your printer is shared as
 * "Receipt Printer), the following commands work:
 *
 *  echo "Hello World" > testfile
 *  copy testfile "\\%COMPUTERNAME%\Receipt Printer"
 *  del testfile
 */
$data = json_decode(file_get_contents('php://input'), true);

try {
    // Enter the share name for your USB printer here
    // $connector = null;
    // $connector = new WindowsPrintConnector("USB002");
    $connector = new WindowsPrintConnector("GOrder");

    $printer = new Printer($connector);
    $printer->text("     Golden Gate Drugstore\n");
    $printer->text("   Patubig, Marilao, Bulacan\n");
    $printer->text("     TEL NO : 09123456789\n");
    $printer->text("----------------------------\n");
    foreach ($data['salesDetails'] as $detail) {
        $printer->text($detail['product_id'] . "    " . $detail['quantity'] . "     " . $detail['amount'] . "\n");
    }
    $printer->text("----------------------------\n");
    $printer->text("\nTOTAL :        " . $data['sales']['total']);
    $printer->text("\nCASH :         " . $data['sales']['payment']);
    $printer->text("\nCHANGE :       " . $data['sales']['change']);
    // $printer->text("Subtotal: ".$data['sales']['subtotal']);
    $printer->text("\n----------------------------\n");
    $printer->text("\nDiscount :     " . $data['sales']['discount']);
    $printer->text("\nVAT :          " . $data['sales']['vat']);
    $printer->text("\n----------------------------\n");
    $printer->text("\nPROCESS BY: " . $data['sales']['processBy']);
    $printer->text("\nDATE : " . $data['sales']['date']);
    $printer->text("\nTIME : " . $data['sales']['time']);
    $printer->text("\nOR# : " . $data['sales']['transactionID']);

    $printer->text("\n\n\n");
    $printer->cut();

    /* Close printer */
    $printer->close();
} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e->getMessage() . "\n";
}
