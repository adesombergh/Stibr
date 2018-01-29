<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Transport extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function lines()
    {
        $lines = $this->getLines();
        return view('lines')->with('lines',$lines);
    }

    public function line($id)
    {
        // $positions = $this->getPositionsByLine($id);
        // return view('line')->with(['line'=>$positions->lines[0]->vehiclePositions,'id'=>$id]);
    }

    public function getLines()
    {
        $json = file_get_contents('routes.json');
        $obj = json_decode($json);
        return $obj->routes;
    }

    /**
     * Gets the current vehicles position in the chosen line.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPositionsByLine($line)
    {
        $options = array(
            CURLOPT_URL             => "https://opendata-api.stib-mivb.be/OperationMonitoring/1.0/VehiclePositionByLine/$line",
            CURLOPT_HTTPHEADER      => array(
                                        'Accept: application/json',
                                        'Authorization: Bearer ec3fbc12115e0fde38402cc5c72fd6af'
                                    ),
            CURLOPT_RETURNTRANSFER  => true // Returns output instead of printing it
        );

        //INIT CURL
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        
        // EXCUTE COMMANDE CURL. Stocke Résultats
        $json = curl_exec($curl);

        //CLOSE CURL (c'est mieux!) 
        curl_close($curl);

        // Decode les résultats pour travailler en php.
        $data = json_decode($json);
        return $data;
    }


}
