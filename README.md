# ONWAY Integration for Laravel

[![Packagist](https://img.shields.io/packagist/v/zgabievi/laravel-onway.svg)](https://packagist.org/packages/zgabievi/laravel-onway)
[![Packagist](https://img.shields.io/packagist/dt/zgabievi/laravel-onway.svg)](https://packagist.org/packages/zgabievi/laravel-onway)
[![license](https://img.shields.io/github/license/zgabievi/laravel-onway.svg)](https://packagist.org/packages/zgabievi/laravel-onway)

## Table of Contents
- [Installation](#installation)
- [Usage](#usage)
    - [Delivery](#delivery)
    - [Check Status](#check-status)
- [Additional Information](#additional-information)
- [Configuration](#configuration)
- [License](#license)

## Installation

To get started, you need to install package:

```shell script
composer require zgabievi/laravel-onway
```

If your laravel version is older than 5.5, then add this to your service providers in *config/app.php*:

```php
'providers' => [
    ...
    Zorb\Onway\OnwayServiceProvider::class,
    ...
];
```

You can publish config file using this command:

```shell script
php artisan vendor:publish --provider="Zorb\Onway\OnwayServiceProvider"
```

This command will copy config file for you.

## Usage

- [Delivery](#delivery)
- [Check Status](#check-status)

### Delivery

Delivery request is done in two parts:

1. Start delivery process and get order id from provider
2. Confirm delivery process by passing order id

```php
use Zorb\Onway\Exceptions\OnwayRequestException;
use Zorb\Onway\Enums\DeliveryZone;
use Zorb\Onway\Facades\Onway;

class DeliveryController extends Controller
{
    //
    public function __invoke()
    {
        // generate locale order id
        $order_id = 1;

        // generate data to deliver from
        $from_location = [
            'ContactName' => 'ჯონ დო',
            'CompanyName' => 'შპს აქმე',
            'AddressLine1' => 'რუსთაველის 52ა',
            'Email' => 'john.doe@email.com',
            'Phone' => '995511000000',
            'Zone' => [
                'ID' => DeliveryZone::Tbilisi
            ]
        ];
    
        // generate data to deliver to
        $to_location = [
            'ContactName' => 'ჯეინ დო',
            'CompanyName' => '',
            'AddressLine1' => 'ცოტნე დადიანის 1ბ',
            'Email' => 'jane.doe@example.com',
            'Phone' => '995511000001',
            'City' => 'თბილისი'
        ];
    
        // approximate weight of items
        $weight = 1.5;

        // list of items that will be delivered
        $products = ['წიგნი', 'სახატავები', 'ქუდი'];

        // quantity of items set
        $quantity = 1;
    
        try {
            $result = Onway::start($order_id, $from_location, $to_location, $weight, $products, $quantity);
    
            if ($result->order_id) {
                $response = Onway::confirm($order_id, 'DECLARED_VALUE');

                if ((int)$response->success === 1) {
                    Log::debug($response->TrackingNumber);

                    // delivery requested
                } else {
                    // delivery request failed
                }
            } else {
                // delivery request failed
            }
        } catch (OnwayRequestException $exception) {
            // delivery request failed
        }
    }
}
```

### Check Status

```php
use Zorb\Onway\Exceptions\OnwayRequestException;
use Zorb\Onway\Facades\Onway;

class DeliveryController extends Controller
{
    //
    public function __invoke() {
        // local order id
        $order_id = 1;

        // tracking number provided by provider
        $tracking_number = 111111;

        try {
            $result = Onway::status($order_id, $tracking_number);

            if ((int)$result->success === 1) {
                $status = (int)$result->status;

                // 1 - Submitted
                // 2 - In Transit
                // 3 - Completed
                // 4 - Canceled
                // 5 - Canceled Billable
            } else {
                // couldn't get delivery status
            }
        } catch (OnwayRequestException $exception) {
            // couldn't get delivery status
        }
    }
}
```

## Additional Information

### DeliveryStatus

Delivery status has its own enum `Zorb\Onway\Enums\DeliveryStatus`

| Key | Value |
| --- | :---: |
| Submitted | 1 |
| InTransit | 2 |
| Completed | 3 |
| Canceled | 4 |
| CanceledBillable | 5 |

### DeliveryZone

Delivery zones has its own enum `Zorb\Onway\Enums\DeliveryZone`

| Key | Value |
| --- | --- |
| Makhinjauri | 98cf112b-26a4-40fd-b010-01b8239a37a8 |
| Tsalka | 768af5b8-8c82-4fe2-9fa9-01be2f188be2 |
| Akhalkalaki | ea1869a5-acff-4bf1-988b-02a8904aa525 |
| Akhaltsikhe | 5c63b4a3-ad0a-453e-9096-05f41b9a9010 |
| Tianeti | 5e627ab7-536d-48ca-bbfa-0635794d6af8 |
| Natakhtari | 4b37b2e0-3fe6-43b7-b98c-0a3ad91f24f6 |
| Borjomi | 5d2e3fb0-7718-402b-becc-0e783afb4849 |
| Kareli | 0b41f02d-ad0a-44e1-907b-166be8dfc420 |
| Gurjaani | cd57ba16-146d-4667-b41e-199069aaa352 |
| Lagodekhi | a90940ee-1a73-4bfe-b867-19d916ca93eb |
| Sachkhere | 77892a24-9e7d-4a54-864a-1aeb5645b363 |
| Zestaphoni | a10a2c6b-b57c-4d8b-9251-213a68db3de0 |
| Rustavi | 223823bc-035a-4be2-b1b3-270695ef204c |
| Dedophlistkaro | d2b9ab57-2590-4b0f-af7a-27d31f36ec55 |
| Tkibuli | 589dfcd8-c7e4-46c8-9720-2d208837f544 |
| Mestia | b65f3a10-1d2b-4320-9164-2d318ffda96b |
| Chkhorotsku | 5f8a5245-f65a-4d56-85ca-323c3f806f9d |
| Zugdidi | f4a61e01-d18f-4f43-80a8-360e7592dc28 |
| Gori | df16d4eb-7bc9-4f37-9466-40377e970ab7 |
| Akhmeta | f1b04e4e-d150-4fd0-9c86-44f79d7bde3a |
| Vani | 9cf7d2ce-b8e7-4ff5-b4f3-457cc6b3ea7c |
| Martvili | 101c049a-f526-4ca3-8c4f-48f7922b7fd4 |
| Sighnaghi | 1d87c3b6-338c-440d-8b7e-4a0d6bb837c5 |
| Tetritskaro | 4ad3d793-f13d-405a-8118-4afadb216474 |
| Tskaltubo | eae65222-bb9a-4015-988d-4c6eabb0641d |
| Ozurgeti | d5c9e241-04f7-4b9a-b48e-4ca88187a26f |
| Kvareli | c5394c54-5400-4997-9b60-51b2b001d5a6 |
| Khoni | 855bb3f3-f6a9-440e-974f-5a2051cb982b |
| Tbilisi | de5d1526-2234-4817-beca-5baa0afed104 |
| Vale | b70e7346-52dc-4ad0-b4fb-5d8e4e4aefda |
| Martkophi | 91742aad-60eb-4441-9211-606612337c3f |
| Mtsketa | 993a492c-fd66-41bc-b5da-64b0cdae3cb0 |
| Tsalenjikha | 9837afce-14d4-43af-b805-6f1c2333f3ab |
| Lanchkhuti | 2e5720f4-e34f-4c3b-93e1-72b4d82c0439 |
| Agara | 2e5d2482-cdc2-47dd-9ec3-74527b808773 |
| Ninothsminda | 75f9ffec-9dde-41c1-91c1-75a97ed19db4 |
| Khulo | 1b3a5e59-7f5a-46c7-9034-7918a74fbf9f |
| Senaki | e0bf0116-b0bb-4a0c-a7b5-7aef1a3a4ecf |
| Terjola | f4d5535e-470f-48e4-8f1f-7bdc497e7cf2 |
| Photi | cab00aec-86ae-4ad0-b104-7e63ba56010d |
| Bolnisi | 3dcb345a-62b3-45e8-bf43-813f739a52e1 |
| Sagarejo | c2a07776-66c4-4a14-abbd-85aefd2b380c |
| Khelvachauri | 857ae23b-3786-4560-8d6b-87ba83b49e07 |
| Dmanisi | d7c4bef2-16bb-4aa1-87fd-88ebab7513fa |
| Telavi | 63df7e8a-5487-4c44-b699-8bae7921c9dc |
| Kazbegi | c3a95ad3-27c8-4cdf-9f1b-8c29dbda925f |
| Abasha | c190aaff-73a1-4ba7-bb5b-9072337f07b5 |
| Dusheti | 5a179595-4a11-49a6-b572-95e539528d42 |
| Ureki | 1f4eb346-3e28-4ce7-90e9-989b4b342e73 |
| Chiatura | 4793015e-fb01-4a21-aa89-992458194bfd |
| Baghdati | e6c39aaa-5f41-4d8a-ae65-9b233623b219 |
| Tskneti | 8fd6929a-9648-4878-a118-9cc91c52e722 |
| Adigeni | 042405bb-f3ba-46bd-8994-a3e1fc6eef8b |
| Tsageri | b576cc8e-21d4-4a21-9c4a-aa4720b9aab1 |
| Lentekhi | 8bac8457-e47c-42e9-8772-ac1ac89c60e0 |
| Samtredia | be63ed85-6e65-40cf-91d3-ad00ce0123a4 |
| Abroad | 9a33e0e5-5ee0-49fe-8fbe-b1661adb49ce |
| Kaspi | a566a247-0a50-4d3a-a19a-b611306c2359 |
| Kutaisi | db4a83ca-e944-4cf1-a80e-b665cf574a93 |
| Gardabani | 25e8fc8d-de02-4d5f-b2e2-b76322046497 |
| Oni | 3a52c55d-1550-4ab6-9d16-b7cf1254f62e |
| Stephantsminda | ea81bc12-a57d-4c8e-ab90-b90f2983b573 |
| Khashuri | 326a4d33-6f94-48fc-a046-bef603efc572 |
| Khobi | f3b6afff-08bb-4cea-b3f7-c5c4000fdf35 |
| Marneuli | cc795eb7-097f-4395-a478-c5cd71ec9d3d |
| Ambrolauri | 778d51df-1a59-43b9-88b5-c7041f3af02f |
| Keda | 470a7491-b3d5-49a9-bcc5-d1e6923e9644 |
| Sartichala | cf3f0a5f-6b85-4d38-8c4b-d4a34a1c7380 |
| Kobuleti | 0f2c95fb-3aeb-43dc-bd46-d4ba89339566 |
| Shuakhevi | 5ace30e0-e118-4bbb-bc77-da0c14e71b5a |
| Chokhatauri | fd585c74-4fe7-4601-a6db-df2285c8904c |
| Kharagauli | 2cd7d305-13d9-4c8e-8bf1-e42476992dea |
| Batumi | 855fd07d-d7c9-491d-bb1d-ecad49f44c02 |
| Aspindza | fe9de29a-8063-4f24-a73e-f9b165bbf8d2 |
| Bakuriani | 83287fa5-db34-4ef5-872f-ffeb51e23c4f |

Delivery zones can be translated in **resources/lang/ka/enums.php**

```php
use Zorb\Onway\Enums\DeliveryZone;

return [
    DeliveryZone::class => [
        DeliveryZone::Makhinjauri => 'მახინჯაური',
        DeliveryZone::Tsalka => 'წალკა',
        DeliveryZone::Akhalkalaki => 'ახალქალაქი',
        DeliveryZone::Akhaltsikhe => 'ახალციხე',
        DeliveryZone::Tianeti => 'თიანეთი',
        DeliveryZone::Natakhtari => 'ნატახტარი',
        DeliveryZone::Borjomi => 'ბორჯომი',
        DeliveryZone::Kareli => 'ქარელი',
        DeliveryZone::Gurjaani => 'გურჯაანი',
        DeliveryZone::Lagodekhi => 'ლაგოდეხი',
        DeliveryZone::Sachkhere => 'საჩხერე',
        DeliveryZone::Zestaphoni => 'ზესტაფონი',
        DeliveryZone::Rustavi => 'რუსთავი',
        DeliveryZone::Dedophlistkaro => 'დედოფლისწყარო',
        DeliveryZone::Tkibuli => 'ტყიბული',
        DeliveryZone::Mestia => 'მესტია',
        DeliveryZone::Chkhorotsku => 'ჩხოროწყუ',
        DeliveryZone::Zugdidi => 'ზუგდიდი',
        DeliveryZone::Gori => 'გორი',
        DeliveryZone::Akhmeta => 'ახმეტა',
        DeliveryZone::Vani => 'ვანი',
        DeliveryZone::Martvili => 'მარტვილი',
        DeliveryZone::Sighnaghi => 'სიღნაღი',
        DeliveryZone::Tetritskaro => 'თეთრიწყარო',
        DeliveryZone::Tskaltubo => 'წყალტუბო',
        DeliveryZone::Ozurgeti => 'ოზურგეთი',
        DeliveryZone::Kvareli => 'ყვარელი',
        DeliveryZone::Khoni => 'ხონი',
        DeliveryZone::Tbilisi => 'თბილისი',
        DeliveryZone::Vale => 'ვალე',
        DeliveryZone::Martkophi => 'მარტყოფი',
        DeliveryZone::Mtsketa => 'მცხეთა',
        DeliveryZone::Tsalenjikha => 'წალენჯიხა',
        DeliveryZone::Lanchkhuti => 'ლანჩხუთი',
        DeliveryZone::Agara => 'აგარა',
        DeliveryZone::Ninothsminda => 'ნინოწმინდა',
        DeliveryZone::Khulo => 'ხულო',
        DeliveryZone::Senaki => 'სენაკი',
        DeliveryZone::Terjola => 'თერჯოლა',
        DeliveryZone::Photi => 'ფოთი',
        DeliveryZone::Bolnisi => 'ბოლნისი',
        DeliveryZone::Sagarejo => 'საგარეჯო',
        DeliveryZone::Khelvachauri => 'ხელვაჩაური',
        DeliveryZone::Dmanisi => 'დმანისი',
        DeliveryZone::Telavi => 'თელავი',
        DeliveryZone::Kazbegi => 'ყაზბეგი',
        DeliveryZone::Abasha => 'აბაშა',
        DeliveryZone::Dusheti => 'დუშეთი',
        DeliveryZone::Ureki => 'ურეკი',
        DeliveryZone::Chiatura => 'ჭიათურა',
        DeliveryZone::Baghdati => 'ბაღდათი',
        DeliveryZone::Tskneti => 'წყნეთი',
        DeliveryZone::Adigeni => 'ადიგენი',
        DeliveryZone::Tsageri => 'ცაგერი',
        DeliveryZone::Lentekhi => 'ლენტეხი',
        DeliveryZone::Samtredia => 'სამტრედია',
        DeliveryZone::Abroad => 'საზღვარგარეთ',
        DeliveryZone::Kaspi => 'კასპი',
        DeliveryZone::Kutaisi => 'ქუთაისი',
        DeliveryZone::Gardabani => 'გარდაბანი',
        DeliveryZone::Oni => 'ონი',
        DeliveryZone::Stephantsminda => 'სტეფანწმინდა',
        DeliveryZone::Khashuri => 'ხაშური',
        DeliveryZone::Khobi => 'ხობი',
        DeliveryZone::Marneuli => 'მარნეული',
        DeliveryZone::Ambrolauri => 'ამბროლაური',
        DeliveryZone::Keda => 'ქედა',
        DeliveryZone::Sartichala => 'სართიჭალა',
        DeliveryZone::Kobuleti => 'ქობულეთი',
        DeliveryZone::Shuakhevi => 'შუახები',
        DeliveryZone::Chokhatauri => 'ჩოხატაური',
        DeliveryZone::Kharagauli => 'ხარაგაული',
        DeliveryZone::Batumi => 'ბათუმი',
        DeliveryZone::Aspindza => 'ასპინძა',
        DeliveryZone::Bakuriani => 'ბაკურიანი',
    ],
];
```

## Configuration

You can configure environment file with following variables:

| Key | Type | Default | Meaning |
| --- | :---: | --- | --- |
| ONWAY_DEBUG | bool | false | This value decides to log or not to log requests. |
| ONWAY_ID | string |  | This is the customer id, which should be generated by onway tech stuff. |
| ONWAY_API_URL | string | https://onway.ge/api/index.php | This is the url provided by onway.ge support. |

## License

[zgabievi/laravel-onway](https://github.com/zgabievi/laravel-onway) is licensed under a [MIT License](https://github.com/zgabievi/laravel-promocodes/blob/master/LICENSE).

