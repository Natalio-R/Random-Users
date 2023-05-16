<?php
class RandomUserAPI
{
    private $apiUrl = 'https://randomuser.me/api/?results=10';

    public function getUsers()
    {
        $curl = curl_init($this->apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true)['results'];
    }
}
?>
