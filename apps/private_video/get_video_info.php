<?php
   
    require '../../vendor/autoload.php';

    
    class get_info
    {
        // Properties
        private $result;
        private $audio;
        private $video;
        private $quality_array;
        private $title;
        private $msg;


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
                $explode = explode('amp;', $explode[0]);
                
                $implode = implode('', $explode);
                $type = stripslashes($implode);
                $result[] = $type;
            }


            return $this->result = $result;
        }


        // Audio/MP4
        private function mp4_audio($data)
        {
            // Explode String
            $explode = explode('mimeType=\"audio\/mp4\"', $data);
            $links = $this->get($explode);


            return $this->audio = $links;
        }


        // Video/Webm
        private function webm_video($data)
        {
            // Explode String
            $explode = explode('mimeType=\"video\/webm\"', $data);


            if(empty($explode[1]))
            {
                $explode = explode('mimeType=\"video\/mp4\"', $data);
            }
            

            $links = $this->get($explode);


            return $this->video = $links;
        }


        // Get Quality Label
        private function quality_label($data)
        {
            // Variables
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
        private function get_title($data)
        {
            // Variables
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
        public function get_response($data)
        {
            // Variables
            $audio = $this->mp4_audio($data);
            $video = $this->webm_video($data);
            $quality = $this->quality_label($data);
            $title = $this->get_title($data);


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