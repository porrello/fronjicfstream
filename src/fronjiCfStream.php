<?php

namespace fronji\fronjicfstream;

use GuzzleHttp\Client;
use fronji\fronjicfstream\Exceptions\InvalidFileException;
use fronji\fronjicfstream\Exceptions\InvalidOriginsException;
use fronji\fronjicfstream\Exceptions\OperationFailedException;
use fronji\fronjicfstream\Exceptions\InvalidCredentialsException;

class fronjicfstream
{
    private $key;
    private $zone;
    private $email;

    /**
     * Initialize fronjicfstream with authentication credentials.
     *
     * @param string $key
     * @param string $zone
     * @param string $email
     */
    public function __construct($key, $zone, $email)
    {
        // dd($key, $zone, $email);
        $key = env('CLOUDFLARE_KEY');
        $zone = env('CLOUDFLARE_ZONE');
        $email = env('CLOUDFLARE_EMAIL');
        $account = env('CLOUDFLARE_ACCOUNT_ID');

        if (empty($key) || empty($zone) || empty($email)) {
            throw new InvalidCredentialsException();
        }

        $this->key = $key;
        $this->zone = $zone;
        $this->email = $email;
        $this->account = $account;

        $this->client = new Client();
    }


    /**
     * Create a live input.
     *
     * @param string $liveId
     *
     * @return json Response body content
     */
    public function createLiveInput($name, $allowedOrigins = null, $requireSignedURLs = false, $mode = "automatic")
    {
        // curl -X POST \ -H "Authorization: Bearer $TOKEN" \https://api.cloudflare.com/client/v4/accounts/$ACCOUNT/stream/live_inputs --data '{"meta": {"name":"test stream 1"},"recording": { "mode": "automatic", "timeoutSeconds": 10, "requireSignedURLs": false, "allowedOrigins": ["*.example.com"] }}'
        $response = $this->client->request('POST', 'https://api.cloudflare.com/client/v4/accounts/' . $this->account . '/stream/live_inputs', [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'meta' => [
                    'name' => $name,
                ],
                'recording' => [
                    'mode' => $mode,
                    'timeoutSeconds' => 10,
                    'requireSignedURLs' => $requireSignedURLs,
                    'allowedOrigins' => $allowedOrigins,
                ],
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }




    /**
     * Update a live input.
     *
     * @param string $liveId
     *
     * @return json Response body content
     */
    public function updateLiveInput($liveId, $name, $allowedOrigins = null, $requireSignedURLs = false, $mode = "automatic")
    {

        // curl -X PUT \ -H "Authorization: Bearer $TOKEN" \https://api.cloudflare.com/client/v4/accounts/$ACCOUNT/stream/live_inputs/:input_id --data '{"meta": {"name":"test stream 1"},"recording": { "mode": "automatic", "timeoutSeconds": 10 }}'
        $response = $this->client->request('PUT', 'https://api.cloudflare.com/client/v4/accounts/' . $this->account . '/stream/live_inputs/' . $liveId, [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'meta' => [
                    'name' => $name,
                ],
                'recording' => [
                    'mode' => $mode,
                    'timeoutSeconds' => 10,
                    'requireSignedURLs' => $requireSignedURLs,
                    'allowedOrigins' => $allowedOrigins,
                ],
            ],
        ]);



        return json_decode($response->getBody()->getContents());
    }


    /**
     * Delete a live input.
     *
     * @param string $liveId
     *
     * @return json Response body content
     */
    public function deleteLiveInput($liveId)
    {

        $response = $this->client->request('DELETE', 'https://api.cloudflare.com/client/v4/accounts/' . $this->account . '/stream/live_inputs/' . $liveId, [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Type' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }


    /**
     * Get the status of a live input.
     *
     * @param string $liveId
     *
     * @return json Response body content
     */
    public function liveInputStatus($liveId)
    {

        $accountID = env('CLOUDFLARE_ACCOUNT_ID');
        $resourceUrl = "https://api.cloudflare.com/client/v4/accounts/$accountID/stream/live_inputs/$liveId";
        $response = $this->client->get($resourceUrl, [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Type' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }


    /**
     * Get the videos of a live input.
     *
     * @param string $liveId
     *
     * @return json Response body content
     */
    public function liveInputVideos($liveId)
    {

        $accountID = env('CLOUDFLARE_ACCOUNT_ID');
        $resourceUrl = "https://api.cloudflare.com/client/v4/accounts/$accountID/stream/live_inputs/$liveId/videos";
        $response = $this->client->get($resourceUrl, [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Type' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }


    /**
     * Add simulcast to a live input.
     *
     * @param string $liveId, $url, $streamKey
     *
     * @return json Response body content
     */
    public function addSimulcastLiveInput($liveId, $url, $streamKey)
    {

        // curl -X POST \
        // --data '{"url": "rtmp://a.rtmp.youtube.com/live2","streamKey": "<redacted>"}' \
        // -H "Authorization: Bearer $TOKEN" \
        // https://api.cloudflare.com/client/v4/accounts/$ACCOUNT/stream/live_inputs/$INPUT_UID/outputs
        $response = $this->client->request('POST', 'https://api.cloudflare.com/client/v4/accounts/' . $this->account . '/stream/live_inputs/' . $liveId . '/outputs', [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'url' => $url,
                'streamKey' => $streamKey,
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }


    /**
     * Delete simulcast to a live input.
     *
     * @param string $liveId, $url, $streamKey
     *
     * @return json Response body content
     */
    public function deleteSimulcastLiveInput($liveId, $OUTPUT_UID)
    {

        //curl -X DELETE \ -H "Authorization: Bearer $TOKEN" \https://api.cloudflare.com/client/v4/accounts/$ACCOUNT/stream/live_inputs/$INPUT_UID/outputs/$OUTPUT_UID

        $response = $this->client->request('DELETE', 'https://api.cloudflare.com/client/v4/accounts/' . $this->account . '/stream/live_inputs/' . $liveId . '/outputs/' . $OUTPUT_UID, [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Type' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }


    /**
     * List simulcasts of a live input.
     *
     * @param string $liveId, $url, $streamKey
     *
     * @return json Response body content
     */
    public function listSimulcastLiveInput($liveId)
    {

        // curl -H "Authorization: Bearer $TOKEN" \ https://api.cloudflare.com/client/v4/accounts/$ACCOUNT/stream/live_inputs/$INPUT_UID/outputs
        $response = $this->client->request('GET', 'https://api.cloudflare.com/client/v4/accounts/' . $this->account . '/stream/live_inputs/' . $liveId . '/outputs/', [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Type' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }


    /**
     * Get the sum of views of last 30 days.
     *
     * @param string $liveId, $url, $streamKey
     *
     * @return json Response body content
     */
    public function analyticsLastMonthViews($uid)
    {

        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));

        $response = $this->client->request('POST', 'https://api.cloudflare.com/client/v4/graphql', [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'query' => "query{
                    viewer{
                        accounts(
                            filter:{
                                accountTag:\"$this->account\"
                                }
                            ) 
                            {streamMinutesViewedAdaptiveGroups(        
                                filter: {         
                                date_lt: \"$tomorrow\"          
                                date_gt: \"$thirtyDaysAgo\"
                                uid:\"$uid\" 
                                }     
                            orderBy:[sum_minutesViewed_DESC]        
                            limit: 10000) 
                            {            
                            sum{          
                                minutesViewed        
                                }        
                                dimensions{          
                                uid        
                                }      
                            }   
                        } 
                    }
                }",
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }


    /**
     * Get live input analytics.
     *
     * @param string $liveId, $url, $streamKey
     *
     * @return json Response body content
     */
    public function analyticsLiveInput($uid)
    {
        $token = "1iV68uvv93ei79I5oVu353BgjNFM1MGAsBb_9jlR";

        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));

        $response = $this->client->request(
            'POST',
            'https://api.cloudflare.com/client/v4/graphql',
            [
                'headers' => [
                    'X-Auth-Key' => $this->key,
                    'X-Auth-Email' => $this->email,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'query' => "query{
                    viewer{
                        accounts(
                            filter:{
                                accountTag:\"$this->account\"
                                }
                            ){videoQualityEventsAdaptiveGroups(        
                                filter: {         
                                date_lt: \"$tomorrow\"          
                                date_gt: \"$thirtyDaysAgo\"
                                uid:\"$uid\" 
                                    }     
                                    limit: 100
                                ){
                                    dimensions{
                                        uid
                                        clientCountryName
                                        date
                                        datetime
                                        datetimeFifteenMinutes
                                        datetimeFiveMinutes
                                        datetimeHalfOfHour
                                        datetimeHour
                                        datetimeMinute
                                        deviceBrowser
                                        deviceOs
                                        deviceType
                                        qualityResolution        
                                    }
                                   
                                }videoPlaybackEventsAdaptiveGroups(        
                                        filter: {         
                                        date_lt: \"$tomorrow\"          
                                        date_gt: \"$thirtyDaysAgo\"
                                        uid:\"$uid\" 
                                            }     
                                            limit: 100
                                        ){
                                            dimensions{
                                                uid
                                                clientCountryName
                                                date
                                                datetime
                                                datetimeFifteenMinutes
                                                datetimeFiveMinutes
                                                datetimeHalfOfHour
                                                datetimeHour
                                                datetimeMinute
                                                deviceBrowser
                                                deviceOs
                                                deviceType
                                            }
                                            sum{
                                                timeViewedMinutes
                                            }

                                } videoBufferEventsAdaptiveGroups(        
                                    filter: {         
                                    date_lt: \"$tomorrow\"          
                                    date_gt: \"$thirtyDaysAgo\"
                                    uid:\"$uid\" 
                                        }     
                                        limit: 100
                                    ){
                                        dimensions{
                                            uid
                                            clientCountryName
                                            date
                                            datetime
                                            datetimeFifteenMinutes
                                            datetimeFiveMinutes
                                            datetimeHalfOfHour
                                            datetimeHour
                                            datetimeMinute
                                            deviceBrowser
                                            deviceOs
                                            deviceType
                                        }
                                        count
                                        avg{
                                            sampleInterval
                                            
                                        }

                            }     
                            } 
                        }
                    }",
                ],
            ]
        );

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Get live input analytics.
     *
     * @param string $liveId, $url, $streamKey
     *
     * @return json Response body content
     */
    public function videoPlaybackEventsAdaptiveGroups($uid)
    {
        $token = "1iV68uvv93ei79I5oVu353BgjNFM1MGAsBb_9jlR";

        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));

        $response = $this->client->request(
            'POST',
            'https://api.cloudflare.com/client/v4/graphql',
            [
                'headers' => [
                    'X-Auth-Key' => $this->key,
                    'X-Auth-Email' => $this->email,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'query' => "query{
                    viewer{
                        accounts(
                            filter:{
                                accountTag:\"$this->account\"
                                }
                            ){videoPlaybackEventsAdaptiveGroups(        
                                        filter: {         
                                        date_lt: \"$tomorrow\"          
                                        date_gt: \"$thirtyDaysAgo\"
                                        uid:\"$uid\" 
                                            }     
                                            limit: 10000
                                        ){
                                            dimensions{
                                                uid
                                                clientCountryName
                                                date
                                                datetime
                                                datetimeFifteenMinutes
                                                datetimeFiveMinutes
                                                datetimeHalfOfHour
                                                datetimeHour
                                                datetimeMinute
                                                deviceBrowser
                                                deviceOs
                                                deviceType
                                            }
                                            sum{
                                                timeViewedMinutes
                                            }

                                

                            }     
                            } 
                        }
                    }",
                ],
            ]
        );

        return json_decode($response->getBody()->getContents());
    }


    // Fields

    // clientCountryName: string!
    // ISO 3166 alpha2 country code from the client

    // date: Date!
    // Request date of the event

    // datetime: Time!
    // Request datetime of the event

    // datetimeFifteenMinutes: Time!
    // Request datetime of the event, truncated to multiple of 15 minutes

    // datetimeFiveMinutes: Time!
    // Request datetime of the event, truncated to multiple of 5 minutes

    // datetimeHalfOfHour: Time!
    // Request datetime of the event, truncated to multiple of 30 minutes

    // datetimeHour: Time!
    // Request datetime of the event, truncated to the hour

    // datetimeMinute: Time!
    // Request datetime of the event, truncated to the minute

    // deviceBrowser: string!
    // Browser of the device used in playback

    // deviceOs: string!
    // OS of the device used in playback

    // deviceType: string!
    // Device type used in playback

    // uid: string!
    // unique id for a video










    /**
     * Get the status of a video.
     *
     * @param string $resourceUrl
     *
     * @return json Response body content
     */
    public function status($streamId)
    {
        $accountID = env('CLOUDFLARE_ACCOUNT_ID');
        $resourceUrl = "https://api.cloudflare.com/client/v4/accounts/$accountID/stream/$streamId";
        $response = $this->client->get($resourceUrl, [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Type' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * Upload a video with a given filepath.
     *
     * @param string $filepath
     *
     * @return string $resourceUrl URL to manage the video resource
     */
    public function upload($filepath)
    {
        $file = fopen($filepath, 'r');
        if (!$file) {
            throw new InvalidFileException();
        }

        $filesize = filesize($filepath);
        $filename = basename($filepath);

        $response = $this->post($filename, $filesize);
        $resourceUrl = $response->getHeader('Location')[0];
        $this->patch($resourceUrl, $file, $filesize);

        return $resourceUrl;
    }

    /**
     * Create a resource on Cloudflare Stream.
     *
     * @param string $filename
     * @param int    $filesize
     *
     * @return object $response Response from Cloudflare
     */
    public function post($filename, $filesize)
    {
        if (empty($filename) || empty($filesize)) {
            throw new InvalidFileException();
        }

        $response = $this->client->post("https://api.cloudflare.com/client/v4/zones/{$this->zone}/media", [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Length' => 0,
                'Tus-Resumable' => '1.0.0',
                'Upload-Length' => $filesize,
                'Upload-Metadata' => "filename {$filename}",
            ],
        ]);

        if (201 != $response->getStatusCode()) {
            throw new OperationFailedException();
        }

        return $response;
    }

    /**
     * Upload the file to Cloudflare Stream.
     *
     * @param string   $resourceUrl
     * @param resource $file        fopen() pointer resource
     * @param int      $filesize
     *
     * @return object $response Response from Cloudflare
     */
    public function patch($resourceUrl, $file, $filesize)
    {
        if (empty($file)) {
            throw new InvalidFileException();
        }

        $response = $this->client->patch($resourceUrl, [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Length' => $filesize,
                'Content-Type' => 'application/offset+octet-stream',
                'Tus-Resumable' => '1.0.0',
                'Upload-Offset' => 0,
            ],
            'body' => $file,
        ]);

        if (204 != $response->getStatusCode()) {
            throw new OperationFailedException();
        }

        return $response;
    }

    /**
     * Delete video from Cloudflare Stream.
     *
     * @param string $resourceUrl
     */
    public function delete($resourceUrl)
    {
        $response = $this->client->delete($resourceUrl, [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Length' => 0,
            ],
        ]);

        if (204 != $response->getStatusCode()) {
            throw new OperationFailedException();
        }
    }

    /**
     * Get embed code for the video.
     *
     * @param string $resourceUrl
     *
     * @return string HTML embed code
     */
    public function code($resourceUrl)
    {
        $response = $this->client->get("{$resourceUrl}/embed", [
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
                'Content-Type' => 'application/json',
            ],
        ]);

        if (200 != $response->getStatusCode()) {
            throw new OperationFailedException();
        }

        return $response->getBody()->getContents();
    }

    /**
     * Set allowedOrigins on the video.
     *
     * @param string $resourceUrl
     * @param string $origins     Comma separated hostnames
     */
    public function allow($resourceUrl, $origins)
    {
        if (false !== strpos($origins, '/')) {
            throw new InvalidOriginsException();
        }

        $videoId = @end(explode('/', $resourceUrl));

        $response = $this->client->post($resourceUrl, [
            'body' => "{\"uid\": \"{$videoId}\", \"allowedOrigins\": [\"{$origins}\"]}",
            'headers' => [
                'X-Auth-Key' => $this->key,
                'X-Auth-Email' => $this->email,
            ],
        ]);

        if (200 != $response->getStatusCode()) {
            throw new OperationFailedException();
        }
    }
}
