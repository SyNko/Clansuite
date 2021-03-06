<?php

/**
 * Koch Framework
 * Jens-Andr� Koch � 2005 - onwards
 *
 * This file is part of "Koch Framework".
 *
 * License: GNU/GPL v2 or any later version, see LICENSE file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Koch\View\Renderer;

use Koch\View\AbstractRenderer;

/**
 * Koch Framework - View Renderer for CSV data.
 *
 * This is a wrapper/adapter for rendering CSV Data. CSV stands for 'comma-seperated-values'.
 * These files are commonly used to export and import data into different databases.
 *
 * @category    Koch
 * @package     View
 * @subpackage  Renderer
 */
class CSV extends AbstractRenderer
{
    private $data = array();
    private $header = array();

    public function initializeEngine()
    {
        return;
    }

    public function configureEngine()
    {
        return;
    }

    /**
     * @param string $template The filepath location of where to save the csv file.
     * @param array|object viewdata
     */
    public function render($template, $viewdata)
    {
        $this->data = $viewdata;
        $this->mssafe_csv($template, $this->data, $this->header);
    }

    /**
     * @param array $data   the array with the data to write as csv
     * @param array $header additional array with column headings (first row of the data)
     */
    public function assign($data, $headers = array())
    {
        $this->data = $data;
        $this->headers = $headers;
    }

    /**
     * mssafe_csv() builds csv files readable by ms-excel/access.
     *
     * Note:
     * 1) For MS Applications the line-endings have to be \r\n not only \n
     * 2) The first value of CSV files are disallowed to be uppercase chars.
     *    If uppercase the csv is mis-interpreted as sylk-format file
     *    @see http://support.microsoft.com/kb/323626
     *
     * @author soapergem[at]gmail[dot]com
     * @link http://de.php.net/manual/de/function.fputcsv.php#90883
     *
     * @param string $filepath location of where the csv file should be saved
     * @param array  $data     the array with the data to write as csv
     * @param array  $header   additional array with column headings (first row of the data)
     */
    private function mssafe_csv($filepath, $data, $header = array())
    {
        $fp = fopen($filepath, 'w');

        if ($fp === true) {
            $show_header = true;

            if (empty($header)) {
                $show_header = false;
                reset($data);
                $line = current($data);

                if (empty($line) == false) {
                    reset($line);
                    $first = current($line);

                    if (mb_substr($first, 0, 2) == 'ID' and preg_match('/["\\s,]/', $first) == false) {
                        array_shift($data);
                        array_shift($line);
                        if (empty($line) == true) {
                            fwrite($fp, '"' . $first . '"' . '\r\n');
                        } else {
                            fwrite($fp, '"' . $first . '",');
                            fputcsv($fp, mb_split(',', $line));
                            fseek($fp, -1, SEEK_CUR);
                            fwrite($fp, "\r\n");
                        }
                    }
                }
            } else {
                reset($header);
                $first = current($header);

                if (mb_substr($first, 0, 2) == 'ID' and preg_match('/["\\s,]/', $first) == false) {
                    array_shift($header);

                    if (empty($header)) {
                        $show_header = false;
                        fwrite($fp, '"' . $first . '"' . '\r\n');
                    } else {
                        fwrite($fp, '"' . $first . '",');
                    }
                }
            }

            if ($show_header) {
                fputcsv($fp, $header);
                fseek($fp, -1, SEEK_CUR);
                fwrite($fp, '\r\n');
            }

            foreach ($data as $line) {
                fputcsv($fp, $line);
                fseek($fp, -1, SEEK_CUR);
                fwrite($fp, '\r\n');
            }
            fclose($fp);
        } else {
            return false;
        }

        return true;
    }
}
