<?php

//============================================================+
// File name   : 2dbarcodes.php
// Version     : 1.0.013
// Begin       : 2009-04-07
// Last Update : 2012-01-12
// Author      : Nicola Asuni - Tecnick.com LTD - Manor Coach House, Church Hill, Aldershot, Hants, GU12 4RQ, UK - www.tecnick.com - info@tecnick.com
// License     : GNU-LGPL v3 (http://www.gnu.org/copyleft/lesser.html)
// -------------------------------------------------------------------
// Copyright (C) 2009-2012  Nicola Asuni - Tecnick.com LTD
//
// This file is part of TCPDF software library.
//
// TCPDF is free software: you can redistribute it and/or modify it
// under the terms of the GNU Lesser General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// TCPDF is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with TCPDF.  If not, see <http://www.gnu.org/licenses/>.
//
// See LICENSE.TXT file for more information.
// -------------------------------------------------------------------
//
// Description : PHP class to creates array representations for
//               2D barcodes to be used with TCPDF.
//
//============================================================+

namespace BG\Barcode;

include_once('modules/QRCode.php');

use BG\Barcode\Modules\QRCode;

/**
 * class Base2DBarcode 1.0.0, based on 2dbarcodes.php v1.0.013 (Nicola Asuni)
 *
 * 2D matrix barcode base class
 *
 * @author Nicola Asuni
 * @author Dinesh Rabara, https://github.com/dineshrabara
 * @author Patrick Paechnatz, https://github.com/paterik
 */
class Base2DBarcode
{
    /**
     * Array representation of barcode.
     * @protected
     */
    protected $barcodeArray = false;

    /**
     * Return an array representations of barcode.
     * @return array
     */
    public function getBarcodeArray()
    {
        return $this->barcodeArray;
    }

    /**
     * setup temporary path for barcode cache
     *
     * @param string $serverPath
     *
     * @throws \Exception
     */
    public function setTempPath($serverPath)
    {
        try {
            if (!file_exists($serverPath)) {
                mkdir($serverPath, 0770, true);
            }
        } catch (\Exception $e) {
            throw new \Exception("An error occurred while creating barcode cache directory at " . $serverPath);
        }
    }
    
    /**
     * Return an HTML Table representation of barcode.
     *
     * @param string $code
     * @param string $type
     * @param int    $w
     * @param int    $h
     * @param string $color
     *
     * @return string
     */
    public function getBarcodeTableHTML($code, $type, $w=10, $h=10, $color='#000000')
    {
        //set barcode code and type
        $this->setBarcode($code, $type);
        $html = '<table cellspacing="0" cellpadding="0" border="0" style="border:none;">' . "\n";
        // print barcode elements
        $y = 0;
        // for each row
        for ($r = 0; $r < $this->barcodeArray['num_rows']; ++$r) {
            $html .= '<tr style="font-size:'.$h.';line-height:'.$h.';height:'. $h .'px;border:none;">' . "\n";
            $x = 0;
            // for each column
            for ($c = 0; $c < $this->barcodeArray['num_cols']; ++$c) {
                if ($this->barcodeArray['bcode'][$r][$c] == 1) {
                    // draw a single barcode cell
                     $html .= '<td style="border-collapse:collapse;font-size:'.$h.'px;line-height:'.$h.'px;background-color:'.$color.';width:'.$w.'px;height:'.$h.'px;">&nbsp;</td>'."\n";
                } else {
                     $html .= '<td style="border-collapse:collapse;font-size:'.$h.'px;line-height:'.$h.'px;background-color:#ffffff;width:'.$w.'px;height:'.$h.'px;">&nbsp;</td>'."\n";
                }
                $x += $w;
            }
            $y += $h;
            $html .= '</tr>' . "\n";
        }
        $html .= '</table>' . "\n";

        return $html;
    }

    /**
     * Return an HTML DIV representation of barcode.
     *
     * @param string $code
     * @param string $type
     * @param int    $w
     * @param int    $h
     * @param string $color
     *
     * @return string
     */
    public function getBarcodeHTML($code, $type, $w=10, $h=10, $color='black')
    {
        //set barcode code and type
        $this->setBarcode($code, $type);
        $html = '<div style="font-size:0;position:relative;width:' . ($w * $this->barcodeArray['num_cols']) . 'px;height:' . ($h * $this->barcodeArray['num_rows']) . 'px;">' . "\n";
        // print barcode elements
        $y = 0;
        // for each row
        for ($r = 0; $r < $this->barcodeArray['num_rows']; ++$r) {
            $x = 0;
            // for each column
            for ($c = 0; $c < $this->barcodeArray['num_cols']; ++$c) {
                if ($this->barcodeArray['bcode'][$r][$c] == 1) {
                    // draw a single barcode cell
                    $html .= '<div style="background-color:' . $color . ';width:' . $w . 'px;height:' . $h . 'px;position:absolute;left:' . $x . 'px;top:' . $y . 'px;">&nbsp;</div>' . "\n";
                }
                $x += $w;
            }
            $y += $h;
        }
        $html .= '</div>' . "\n";

        return $html;
    }

    /**
     * Set the barcode.
     *
     * @param string $code
     * @param string $type
     */
    public function setBarcode($code, $type)
    {
        $mode = explode(',', $type);
        $qrtype = strtoupper($mode[0]);
        switch ($qrtype) {
            case 'QRCODE': // QR-CODE
                if (!isset($mode[1]) || (!in_array($mode[1], array('L', 'M', 'Q', 'H')))) {
                    $mode[1] = 'L'; // Ddefault: Low error correction
                }
                $qrcode = new QRCode($code, strtoupper($mode[1]));
                $this->barcodeArray = $qrcode->getBarcodeArray();
                $this->barcodeArray['code'] = $code;
                break;

            case 'RAW':
            case 'RAW2': // RAW MODE
                // remove spaces
                $code = preg_replace('/[\s]*/si', '', $code);
                if (strlen($code) < 3) {
                    break;
                }
                if ($qrtype == 'RAW') {
                    // comma-separated rows
                    $rows = explode(',', $code);
                } else {
                    // rows enclosed in square parentheses
                    $code = substr($code, 1, -1);
                    $rows = explode('][', $code);
                }
                $this->barcodeArray['num_rows'] = count($rows);
                $this->barcodeArray['num_cols'] = strlen($rows[0]);
                $this->barcodeArray['bcode'] = array();
                foreach ($rows as $r) {
                    $this->barcodeArray['bcode'][] = str_split($r, 1);
                }
                $this->barcodeArray['code'] = $code;
                break;

            default:
                $this->barcodeArray = false;
                break;
        }
    }
}
