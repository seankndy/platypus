# platypus
Basic usage:
```
require 'vendor/autoload.php';

use \SeanKndy\Platypus\Parameter;
use \SeanKndy\Platypus\ParameterArray;

try {
    $client = new \SeanKndy\Platypus\Client(
        'your.plat.server.com', 5566,
        'Some Staff', 'password'
    );
    $req = $client->createRequest('AddToPlat') // AddToPlat is a Plat API action
            ->addParameter(new Parameter('datatype', 'XML'))
            ->addProperty(new Parameter('billingmeth', 'PAPER'))
            ->addProperty(new Parameter('name2', 'Test User'))
            ->addProperty(new Parameter('email', 'testing@test.com'))
            ->addProperty(new Parameter('active', 'Y'))
            ->addProperty(new Parameter('phone', '1112223333'))
            ->addProperty(new Parameter('fax', ''))
            ->addProperty(new Parameter('username', 'testing'))
            ->addProperty(new Parameter('password', '12345'))
            ->addProperty(new Parameter('address', '1234 Test Dr.'))
            ->addProperty(new Parameter('address2', ''))
            ->addProperty(new Parameter('city', 'Gillette'))
            ->addProperty(new Parameter('state', 'WY'))
            ->addProperty(new Parameter('zip', '82718'));
$response = $client->sendRequest($req); // \SeanKndy\Platypus\Response object
```
