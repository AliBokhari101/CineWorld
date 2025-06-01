
// Add to composer.json
{
  "require": {
    "cloudinary/cloudinary_php": "^2"
  }
}
// Then run:
php composer.phar install

use Cloudinary\Configuration\Configuration;

Configuration::instance([
  'cloud' => [
    'cloud_name' => 'dnhkcbjhx',
    'api_key' => '678963383357473',
    'api_secret' => '<your_api_secret>'],
  'url' => [
    'secure' => true]
]);