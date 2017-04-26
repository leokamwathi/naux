<?php


function getLatLng($address){
        if(isset($address) && $address != ''){
            $geodata = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$address);
            $jsondata=json_decode($geodata);
            return ($jsondata->results[0]->geometry->location->lat.",".$jsondata->results[0]->geometry->location->lng);
        }else{
            return "";
        }
}


function GetCityCountry($geoLoc){
try{
//$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$geoLoc."&sensor=true&key=".$_ENV["google_maps_key"];
//TODO:make this work with the googlemap key. ^^^^^^ see above link
$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$geoLoc."&sensor=true";
    //google_maps_key

    $data = @file_get_contents($url);
    $jsondata = json_decode($data,true);
  if(is_array($jsondata) && $jsondata['status'] == "OK")
    {
	$location = array();
	$results = $jsondata['results']['0'];
	//print_r($results);
  foreach ($results['address_components'] as $component) {
    switch ($component['types']) {
      case in_array('street_number', $component['types']):
        $location['street_number'] = $component['long_name'];
        break;
      case in_array('route', $component['types']):
        $location['street'] = $component['long_name'];
        break;
      case in_array('sublocality', $component['types']):
        $location['sublocality'] = $component['long_name'];
        break;
      case in_array('locality', $component['types']):
        $location['locality'] = $component['long_name'];
        break;
      case in_array('administrative_area_level_2', $component['types']):
        $location['admin_2'] = $component['long_name'];
        break;
      case in_array('administrative_area_level_1', $component['types']):
        $location['admin_1'] = $component['long_name'];
        break;
      case in_array('postal_code', $component['types']):
        $location['postal_code'] = $component['long_name'];
        break;
      case in_array('country', $component['types']):
        $location['country'] = $component['long_name'];
        break;
    }
	}

	 $city = "";
  $country = $location['country'];

  if($location['locality'] != ''){
  $city = $location['locality'];
  }else if($location['sublocality'] != ''){
    $city = $location['sublocality'];
  }else if($location['admin_1'] != ''){
    $city = $location['admin_1'];
  }else if($location['street'] != ''){
    $city = $location['street'];
  }else{
   $city = $location['country'];
  }
 return($city.",".$country);
//print_r( $location);
  }else{
  return("false");
  }
} catch (Exception $e) {
 return("false");
}
}

function getMYLatLng(){
    $geocodestr = getField('findgeolocation');
    if(isset($geocodestr) && $geocodestr != ''){
        return $geocodestr;
    }else{
        $geocodestr = getLatLng(getField('findlocation'));
        if(isset($geocodestr) && $geocodestr != ''){
            return $geocodestr;
        }else{
            return "";
        }
    }
}

function findPlace($find){
/*
******all google types****
"accounting","airport","amusement_park","aquarium","art_gallery","atm","bakery","bank","bar","beauty_salon","bicycle_store","book_store","bowling_alley","bus_station","cafe","campground","car_dealer","car_rental","car_repair","car_wash","casino","cemetery","church","city_hall","clothing_store","convenience_store","courthouse","dentist","department_store","doctor","electrician","electronics_store","embassy","establishment","finance","fire_station","florist","food","funeral_home","furniture_store","gas_station","general_contractor","grocery_or_supermarket","gym","hair_care","hardware_store","health","hindu_temple","home_goods_store","hospital","insurance_agency","jewelry_store","laundry","lawyer","library","liquor_store","local_government_office","locksmith","lodging","meal_delivery","meal_takeaway","mosque","movie_rental","movie_theater","moving_company","museum","night_club","painter","park","parking","pet_store","pharmacy","physiotherapist","place_of_worship","plumber","police","post_office","real_estate_agency","restaurant","roofing_contractor","rv_park","school","shoe_store","shopping_mall","spa","stadium","storage","store","subway_station","synagogue","taxi_stand","train_station","transit_station","travel_agency","university","veterinary_care","zoo","administrative_area_level_1","administrative_area_level_2","administrative_area_level_3","administrative_area_level_4","administrative_area_level_5","colloquial_area","country","establishment","finance","floor","food","general_contractor","geocode","health","intersection","locality","natural_feature","neighborhood","place_of_worship","political","point_of_interest","post_box","postal_code","postal_code_prefix","postal_code_suffix","postal_town","premise","room","route","street_address","street_number","sublocality","sublocality_level_4","sublocality_level_5","sublocality_level_3","sublocality_level_2","sublocality_level_1","subpremise"
*/

//"find hotel near"

$GLOBALS['status_places'] = basicReply('Hi '.$GLOBALS['username'].', \nSorry we could not find any places nearby matching '.$find);

$find = "Find ".$find;

$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"Authorization: Bearer 67IYBDEUGKA7TEJC2J46PUHWYJRAGM2G"
  )
);

$context = stream_context_create($opts);
$text  = str_replace(' ', '+', trim($text));
$file = file_get_contents('https://api.wit.ai/message?v=20170426&q='.$text, false, $context);

$quest = json_decode($file);
$intent = $quest->entities->intent[0]->value;
$search_query = "";
$more_search_query="";
$location="";
foreach($quest->entities->local_search_query as $searchArray){
	if ($search_query == ""){
		$search_query = $searchArray->value;
	}else{
		$more_search_query = $more_search_query."+".$searchArray->value;
	}
}

foreach($quest->entities->location as $LocationArray){
$location = $location."+".$LocationArray->value;
}

if($location!=""){
    $location = getLatLng($location);
}

$isFind = true;
if($intent !='find'){
        $GLOBALS['status_places'] = basicReply('Hi '.$GLOBALS['username'].', \nSorry we could not find any places nearby matching '.$find);
        $isFind = false;
}elseif($search_query==""){
        $GLOBALS['status_places'] = basicReply('Hi '.$GLOBALS['username'].', \nSorry we could not find any places nearby matching '.$find.'.\nYou must enter something to find.');
        $isFind = false;
}elseif($location==""){
//NOW THIS
//Do i have a pre defined location
        $location = getMYLatLng();
        if($location==""){
                $GLOBALS['status_places'] = basicReply('Hi '.$GLOBALS['username'].', \nSorry we could not find any places nearby matching '.$find);
                $isFind = false;
        }
}

if(isset($geocodestr) && $geocodestr != '' && $isFind){
    //$geoURL = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='.$geocodestr.'&radius=5000&type='.$find.'&keyword='.$find.'&key='.$_ENV['google_places_key'];
    $placesTextSearch='https://maps.googleapis.com/maps/api/place/textsearch/json?query='.$find.'&key='.$_ENV['google_places_key'];
    $placesNearbySearch = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='.$geocodestr.'&radius=50000&keyword='.$find.'&key='.$_ENV['google_places_key'];
    $placesNearbySearchRanked = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?rankby=distance&location='.$geocodestr.'&radius=50000&keyword='.$find.'&key='.$_ENV['google_places_key'];
    $results =  file_get_contents($placesNearbySearch);
    $jsondata = json_decode($results);
    $geoURL = $placesTextSearch;
    if($jsondata->status != "OK")
      {
          $results =  file_get_contents($placesNearbySearchRanked);
          $jsondata = json_decode($results);
          $geoURL = $placesNearbySearchRanked;
      }

      if($jsondata->status != "OK")
        {
            $results =  file_get_contents($placesTextSearch);
            $jsondata = json_decode($results);
            $geoURL = $placesTextSearch;
        }
//if($GLOBALS['username']=='Leo'){
    $geolog= $geolog."  <<< ".$geoURL." >>>  ";
//}
      if($jsondata->status == "OK")
        {
            $GLOBALS['status_places'] =
            '{"recipient": {
            "id": "' . $GLOBALS['sid'] . '"
            },
            "message": {
            "attachment": {
                "type": "template",
                "payload": {
                    "template_type": "generic","elements": [';
           $count = 0;
    	   foreach ($jsondata->results as $component) {
    		$geolatx = $component->geometry->location->lat.",".$component->geometry->location->lng;
    		//markers=icon:https://maps.gstatic.com/mapfiles/place_api/icons/school-64.png%7Cshadow:true
    		//$imgurl="https://maps.googleapis.com/maps/api/staticmap?center=".$geolatx."&size=500x260&key=AIzaSyDrw7vZP5NQ6gC9LPpxYL8AdEneojJKTpo".$marker="&markers=".$geolatx;
            //https://maps.googleapis.com/maps/api/staticmap?center=Dandora%20Girl%27s%20Secondary%20School%20nairobi%20kenya&size=500x260&key=AIzaSyDrw7vZP5NQ6gC9LPpxYL8AdEneojJKTpo&markers=Dandora%20Girl%27s%20Secondary%20School&zoom=17
            $geolatx = $component->name."+,".$component->vicinity."+,".$geoclocation;
            $imgurl="https://maps.googleapis.com/maps/api/staticmap?center=".$geolatx."&size=500x260&key=AIzaSyDrw7vZP5NQ6gC9LPpxYL8AdEneojJKTpo".$marker="&markers=".$geolatx."&zoom=17";

            $element = '
           {
               "title": "'.$component->name.'",
               "subtitle": "'.$component->vicinity.'",
               "image_url": "'.$imgurl.'",
               "buttons": [
                   {
                       "type":"element_share"
                   }
               ]
           }';
           if($count == 0){
               $hasRows = true;
               $GLOBALS['status_places'] = $GLOBALS['status_places'].$element;
           }elseif($count < 8){
               $GLOBALS['status_places'] = $GLOBALS['status_places'].",".$element;
           }else{
               break;
           }
           $count = $count + 1 ;
    	}
            $GLOBALS['status_places'] = $GLOBALS['status_places'].']}}}}';
            if($count == 0){
                $geolog= $geolog.'{ZERO COUNT-DO TEXT SEARCH}'.$geocodestr.$find;
                if($GLOBALS['username']!='Leo'){ $geolog = "";}
                $GLOBALS['status_places'] = basicReply('Hi '.$GLOBALS['username'].', \nSorry we could not find any places nearby matching '.$find.$geolog);
                   return false;
            }else{
                return true;
            }
    	}else{
            $geolog= $geolog.'{STATUS NOT OK} = [[['.$jsondata->status."]]]".$geocodestr.$find;
            if($GLOBALS['username']!='Leo'){ $geolog = "";}
            $GLOBALS['status_places'] = basicReply('Hi '.$GLOBALS['username'].', \nSorry we could not find any places nearby matching '.$find.$geolog);
    	    return false;
    	}
    }else{
        return false;
    }
    }



 ?>
