// include your composer dependencies
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setApplicationName("Anime");
$client->setDeveloperKey("AIzaSyA9VECd6_keyzCsId7hxxU-bcVAijMv6SM");

$service = new Google_Service_Books($client);
$optParams = array('filter' => 'free-ebooks');
$results = $service->volumes->listVolumes('Henry David Thoreau', $optParams);

foreach ($results as $item) {
  echo $item['volumeInfo']['title'], "<br /> \n";
}