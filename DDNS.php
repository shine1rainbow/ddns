<?php

require __DIR__ . '/vendor/autoload.php';

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class DDns {

    private $alibabaCloud;

    /**
     * 构造方法
     *
     * @param string $accessKeyId
     * @param string $accessKeySecret
     */
    public function __construct(string $accessKeyId, string $accessKeySecret)
    {
        AlibabaCloud::accessKeyClient($accessKeyId, $accessKeySecret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
    }

    public function getRecord(array $params)
    {
        try {
            $result = AlibabaCloud::rpc()
              ->product('Alidns')
              //->scheme('https') // https | http
              ->version('2015-01-09')
              ->action('DescribeDomainRecords')
              ->method('POST')
              ->options(['query' => [
				'DomainName' => $params['DomainName']
			  ]
			  ])->request();

            $response = $result->toArray();
            $recordList = $response['DomainRecords']['Record'];

			$res = null;

			foreach ($recordList as $key => $record) {
				if ($record['Type'] === $params['Type'] && $params['Prefix'] === $record['RR']) {
					$res = $record;
				}
			}

			if ($res === null) {
				return null;
			}

			return $res;

        } catch (ClientException $e) {
            //$e->getErrorMessage() . PHP_EOL;
            return null;
        } catch (ServerException $e) {
            //$e->getErrorMessage() . PHP_EOL;
            return null;
        }
    }

    public function updateRecord(array $params)
    {
        try {
            $result = AlibabaCloud::rpc()
              ->product('Alidns')
              //->scheme('https') // https | http
              ->version('2015-01-09')
              ->action('UpdateDomainRecord')
              ->method('POST')
              ->options(['query' => [
					'Type' => $params['Type'],
					'RR' => $params['RR'],
					'RecordId' => $params['RecordId'],
					'Value' => $params['Value'],
				]
			  ])
              ->request();
		
            $response = $result->toArray();

			return $response;

        } catch (ClientException $e) {
            //$e->getErrorMessage() . PHP_EOL;
            return $e->getErrorMessage();
            //return null;
        } catch (ServerException $e) {
            //$e->getErrorMessage() . PHP_EOL;
            return $e->getErrorMessage();
            //return null;
        }
    }
}

