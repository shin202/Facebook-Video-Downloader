<?php
   
    require '../../vendor/autoload.php';
    use Symfony\Component\HttpClient\HttpClient;
    use Symfony\Component\HttpClient\Exception\RedirectionException;


    class get_info
    {
        // Properties
        private $data;
        private $result;
        private $audio;
        private $video;
        private $quality_array;
        private $title;
        private $msg;


        // Send Request
        private function send_request($url)
        {
            // If URL is group or person video, change user agent request.
            $patt = '/^(?:https\:\/\/www\.facebook\.com\/)?([\w]*\/)?(?:videos\/)?([\w]*)\/$/';
            $user_agent = '';


            if(preg_match($patt, $url))
            {
                $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 12_1) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.2 Safari/605.1.15';
            }
            else
            {
                $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36';
            }


            // Create request.
            $client = HttpClient::create();


            // Check Redirect.
            try
            {
                $response = $client->request('GET', $url, ['max_redirects' => 0])->getContent();
            }
            catch(RedirectionException $err)
            {
                $redirect_url = $err->getResponse()->getInfo()['redirect_url'];
            }


            if(isset($redirect_url))
            {
                $url = $redirect_url;
            }


            $client = HttpClient::create([
                'headers' => [
                    'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                    'user-agent' => $user_agent,
                    'cookie' => ''
                ],
                'verify_peer' => false
            ]);


            $response = $client->request('GET', $url);
            $data = $response->getContent();


            return $this->data = $data;
        }


        // Explode Data
        private function get($explode)
        {
            // Variables
            $patt = '/\\u003CBaseURL\>([^\>]+)/';
            $array = [];
            $type = '';
            $result = [];

            
            // Explode String -1-
            for($i = 1; $i < sizeof($explode); $i++)
            {
                $type = $explode[$i];
                if(preg_match($patt, $type, $matches))
                {
                    $matches = $matches[1];
                }
                else
                {
                    break;
                }


                $array[] = $matches;
            }


            // Explode String -2-
            for($j = 0; $j < sizeof($array); $j++)
            {
                $explode = explode('\u003C\/BaseURL', $array[$j]);
                $type = stripslashes($explode[0]);
                $result[] = $type;
            }


            return $this->result = $result;
        }


        // Audio/MP4
        private function mp4_audio($url)
        {
            $data = $this->send_request($url);
            $explode = explode('mimeType=\"audio\/mp4\"', $data);
            $links = $this->get($explode);


            return $this->audio = $links;
        }

        // Video/Webm
        private function webm_video($url)
        {
            // If URL is group or person video, change separator.
            $patt = '/^(?:https\:\/\/www\.facebook\.com\/)?([\w]*\/)?(?:videos\/)?([\w]*)\/$/';


            if(preg_match($patt, $url))
            {
                $separator = 'mimeType=\"video\/mp4\"';
            }
            else
            {
                $separator = 'mimeType=\"video\/webm\"';
            }


            $data = $this->send_request($url);
            $explode = explode($separator, $data);
            $links = $this->get($explode);


            return $this->video = $links;
        }

        // Get Quality Label
        private function quality_label($url)
        {
            // Variables
            $data = $this->send_request($url);
            $explode = explode('FBQualityClass=\"', $data);
            $patt = '/FBQualityLabel=\\\"([\w]*)/';
            $quality_array = [];


            // Explode String
            for($i = 1; $i < sizeof($explode); $i++)
            {
                if(preg_match($patt, $explode[$i], $matches))
                {
                    $matches = $matches[1];
                }
                else
                {
                    break;
                }


                $quality_array[] = $matches;
            }


            return $this->quality_array = $quality_array;
        }


        // Get Title
        private function get_title($url)
        {
            // Variables
            $data = $this->send_request($url);
            $title = '';
            $pattern_1 = '/<title>(.*?)<\/title>/';
            $pattern_2 = '/title id="pageTitle">(.*?)<\/title>/';
    

            if (preg_match($pattern_1, $data, $matches)) 
            {
                $title = $matches[1];
            } 
            elseif (preg_match($pattern_2, $data, $matches)) 
            {
                $title = $matches[1];
            }
    

            return $this->title = html_entity_decode($title, ENT_QUOTES);
        }


        // Response
        public function get_response($url)
        {
            // Variables
            $audio = $this->mp4_audio($url);
            $video = $this->webm_video($url);
            $quality = $this->quality_label($url);
            $title = $this->get_title($url);


            $msg = [];

            // Video Title
            $msg["title"] = $title;


            // Audio
            if(sizeof($audio) == 1)
            {
                $msg["links"]["Audio"] = $audio;
            }
            else
            {
                for($i = 0; $i < sizeof($audio); $i++)
                {
                    $msg["links"]["audio: $i"] = $audio[$i];
                }
            }


            // Video
            for($j = 0; $j < sizeof($video); $j++)
            {
                $msg["links"]["video $j: $quality[$j]"] = $video[$j];
            }


            return $this->msg = $msg;
        }

    }
?>