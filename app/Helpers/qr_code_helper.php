<?php


use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

function set_qr_code($url, $logo, $text)
{
    $builder = new Builder(
        writer: new PngWriter(),
        writerOptions: [],
        validateResult: false,
        data: $url,
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::High,
        size: 500,
        margin: 10,
        roundBlockSizeMode: RoundBlockSizeMode::Margin,
        // logoPath: '/' . $logo . '.png',
        logoResizeToWidth: 50,
        logoPunchoutBackground: true,
        labelText: $text,
        labelFont: new OpenSans(20),
        labelAlignment: LabelAlignment::Center
    );

    $result = $builder->build();
    $dataUri = $result->getDataUri();

    return $dataUri;
}
