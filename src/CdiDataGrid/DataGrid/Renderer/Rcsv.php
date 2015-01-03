<?php

namespace CdiDataGrid\DataGrid\Renderer;

use CdiDataGrid\Grid;
use CdiDataGrid\DataGrid\Renderer\AbstractRenderer;
use Zend\Http\Response\Stream as ResponseStream;
use Zend\Http\Headers;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Renderer
 *
 * @author cincarnato
 */
class Rcsv extends AbstractRenderer {

    //put your code here
    protected $separator = ";";

    public function deploy($grid, $separator = ";") {
        $this->separator = $separator;
        $this->row = $grid->getRow();



        $path = "public/download";

        $saveFilename = "grid";


        if (!$handle = fopen($path . "/" . $saveFilename, "w")) {
            echo "Cannot open file in handle";
            exit;
        }

        foreach ($grid->getColumnCollection() as $column) {
            if (!$column->getHidden()) {
                $fields[0][] = $column->getName();
            }
        }

        $i = 1;
        foreach ($grid->getRow() as $data) {
            foreach ($grid->getColumnCollection() as $column) {
                if (!$column->getHidden()) {
                    if ($column->getType() == "text") {
                        $fields[$i][] = utf8_decode(nl2br($data[$column->getName()]));
                    }
                    if ($column->getType() == "boolean") {
                        $fields[$i][] = nl2br($data[$column->getName()]);
                    }

                    if ($column->getType() == "datetime") {
                        $fields[$i][] = $data[$column->getName()]->format($column->getFormatDatetime());
                    }
                }
            }
            $i++;
        }

        foreach ($fields as $f) {
            fputcsv($handle, $f, $this->separator);
        }
        fclose($handle);


        $response = new ResponseStream();
        $response->setStream(fopen($path . '/' . $saveFilename, 'r'));

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Type' => array(
                'application/force-download',
                'application/octet-stream',
                'application/download'
            ),
            'Content-Length' => filesize($path . '/' . $saveFilename),
            'Content-Disposition' => 'attachment;filename=' . $saveFilename . '.csv',
            'Cache-Control' => 'must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Thu, 1 Jan 1970 00:00:00 GMT'
        ));

        $response->setHeaders($headers);

        return $response;
    }

    
    public function getSeparator() {
        return $this->separator;
    }

    public function setSeparator($separator) {
        $this->separator = $separator;
    }


}

?>
