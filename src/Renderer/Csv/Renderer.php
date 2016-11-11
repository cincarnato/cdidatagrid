<?php

namespace CdiDataGrid\Renderer\Csv;

use CdiDataGrid\Renderer\AbstractRenderer;

/**
 * Description of Renderer
 *
 * @author cincarnato
 */
class Renderer extends AbstractRenderer {

    //put your code here


    public function deploy() {
        $path = "public/download";

        $saveFilename = "AlliedScan_" . $aData["scanid"] . '.csv';


        if (!$handle = fopen($path . "/" . $saveFilename, "w")) {
            echo "Cannot open file in handle";
            exit;
        }


        foreach ($this->list as $fields) {
            fputcsv($handle, $fields, ",");
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

}

?>
