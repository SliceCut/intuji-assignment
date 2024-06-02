<?php

namespace App\Services;

use App\Exceptions\HttpErrorException;

class HttpService
{
    public function get(
        string $url,
        array $query = [],
        array $headers = []
    ): array {
        $headers = array_merge([
            'Content-Type: application/json',
        ], $headers);

        // Initialize cURL session
        $curl = curl_init($url . "?" . http_build_query($query));

        // Set options for the cURL session
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // Execute the cURL session
        $response = curl_exec($curl);

        // Check for errors
        if ($response === false) {
            $error = curl_error($curl);
            $error_code = curl_errno($curl); // Get the cURL error code
            curl_close($curl);
            throw new HttpErrorException(
                message: "Failed to fetch data from $url: $error",
                code: $error_code
            );
        }

        $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response_array = json_decode($response, true);

        if ($response_array === null) {
            throw new HttpErrorException("Failed to decode JSON response from $url", $http_status_code);
        }

        return [
            "status" => $http_status_code,
            "payload" => $response_array
        ];
    }

    public function post(
        string $url,
        array $request_data,
        array $headers = []
    ): array {

        $headers = array_merge([
            'Content-Type: application/json'
        ], $headers);

        // Convert the request body array to JSON format
        $json_post_data = json_encode($request_data);

        // Initialize cURL session
        $curl = curl_init($url);

        // Set options for the cURL session
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($curl, CURLOPT_POST, true); // Set request method to POST
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_post_data); // Set the request body
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // Execute the cURL session
        $response = curl_exec($curl);

        // Check for errors
        if ($response === false) {
            $error = curl_error($curl);
            $error_code = curl_errno($curl); // Get the cURL error code
            curl_close($curl);
            throw new HttpErrorException("Failed to create data from $url: $error", $error_code);
        }

        // Get the HTTP status code
        $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the cURL session
        curl_close($curl);

        // Decode JSON response to an array
        $response_array = json_decode($response, true);

        // Check if decoding was successful
        if ($response_array === null) {
            throw new HttpErrorException("Failed to decode JSON response from $url", $http_status_code);
        }

        return [
            "status" => $http_status_code,
            "payload" => $response_array
        ];
    }

    public function update(
        string $url,
        array $request_data,
        array $headers = []
    ): array {

        $headers = array_merge([
            'Content-Type: application/json'
        ], $headers);

        // Convert the request body array to JSON format
        $json_post_data = json_encode($request_data);

        // Initialize cURL session
        $curl = curl_init($url);

        // Set options for the cURL session
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT"); // Set request method to PUT
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_post_data); // Set the request body
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // Execute the cURL session
        $response = curl_exec($curl);

        // Check for errors
        if ($response === false) {
            $error = curl_error($curl);
            $error_code = curl_errno($curl); // Get the cURL error code
            curl_close($curl);
            throw new HttpErrorException("Failed to update data from $url: $error", $error_code);
        }

        // Get the HTTP status code
        $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the cURL session
        curl_close($curl);

        // Decode JSON response to an array
        $response_array = json_decode($response, true);

        // Check if decoding was successful
        if ($response_array === null) {
            throw new HttpErrorException("Failed to decode JSON response from $url", $http_status_code);
        }

        return [
            "status" => $http_status_code,
            "payload" => $response_array
        ];
    }

    public function delete(
        string $url,
        array $headers = []
    ): array {

        $headers = array_merge([
            'Content-Type: application/json'
        ], $headers);

        // Initialize cURL session
        $curl = curl_init($url);

        // Set options for the cURL session
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE"); // Set request method to PUT
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // Execute the cURL session
        $response = curl_exec($curl);

        // Check for errors
        if ($response === false) {
            $error = curl_error($curl);
            $error_code = curl_errno($curl); // Get the cURL error code
            curl_close($curl);
            throw new HttpErrorException("Failed to delete data from $url: $error", $error_code);
        }

        // Get the HTTP status code
        $http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Close the cURL session
        curl_close($curl);

        // Decode JSON response to an array
        $response_array = json_decode($response, true);

        return [
            "status" => $http_status_code,
            "payload" => $response_array
        ];
    }
}
