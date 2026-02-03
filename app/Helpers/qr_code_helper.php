<?php

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

function set_qr_code($url, $logo, $text)
{
    $writer = new PngWriter();
    $qrCode = QrCode::create($url)
        // ->setEncoding(new Encoding('UTF-8'))
        // ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
        ->setSize(100)
        ->setMargin(($logo == 'ekstra' ? 5 : 0))
        // ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
        ->setForegroundColor(new Color(0, 0, 0));
    // ->setBackgroundColor(new Color(255, 255, 255));

    $logo = Logo::create($logo . '.png')
        ->setResizeToWidth(($text == 'Ppdb' ? 5 : 25))
        ->setPunchoutBackground(false);

    $label = Label::create($text)
        ->setTextColor(new Color(99, 99, 99));

    $result = $writer->write($qrCode, $logo);


    $qr = $result->getDataUri();


    return $qr;
}


/**
 * Generate QRIS dinamis dengan opsi styling
 *
 * @param string $merchantId
 * @param int    $amount
 * @param string $merchantName
 * @param string $merchantCity
 * @return array ['payload' => string, 'image' => base64 string]
 */
function generate_qris($merchantId, $amount, $merchantName = 'Merchant', $merchantCity = 'Jakarta')
{
    // Susun payload QRIS (TLV EMVCo)
    $payload = "000201010212"
        . "26360010ID.COMPANY0115" . $merchantId
        . "52040000"
        . "5303360"
        . "54" . strlen($amount) . $amount
        . "5802ID"
        . "59" . str_pad(strlen($merchantName), 2, '0', STR_PAD_LEFT) . $merchantName
        . "60" . str_pad(strlen($merchantCity), 2, '0', STR_PAD_LEFT) . $merchantCity
        . "6304";

    $payload .= qris_crc16($payload);

    // Buat QRCode object
    $qrCode = QrCode::create($payload)
        ->setEncoding(new Encoding('UTF-8'))
        ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
        ->setSize(300)
        ->setMargin(10)
        ->setForegroundColor(new Color(0, 0, 0))
        ->setBackgroundColor(new Color(255, 255, 255));

    // Tambahkan logo (opsional)
    // $logo = Logo::create(__DIR__.'/logo.png')->setResizeToWidth(50);
    // $qrCode->setLogo($logo);

    // Tambahkan label (opsional)
    // $label = Label::create('Scan QRIS')->setTextColor(new Color(0, 0, 0));
    // $qrCode->setLabel($label);

    // Render ke PNG
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    return [
        'payload' => $payload,
        'image'   => base64_encode($result->getString())
    ];
}

/**
 * Hitung CRC-16/CCITT untuk QRIS
 */
function qris_crc16($payload)
{
    $crc = 0xFFFF;
    for ($i = 0; $i < strlen($payload); $i++) {
        $crc ^= ord($payload[$i]) << 8;
        for ($j = 0; $j < 8; $j++) {
            if ($crc & 0x8000) {
                $crc = ($crc << 1) ^ 0x1021;
            } else {
                $crc <<= 1;
            }
            $crc &= 0xFFFF;
        }
    }
    return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
}
