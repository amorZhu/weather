<?php
/**
 * 天气接口类
 * @filename Weather.php
 * @author zhushaolong(zhushaolong@sdxxtop.com)
 * @date 2021/3/8
 * @time 下午4:10
 */

namespace Amorzhu\Weather;

use GuzzleHttp\Client;

use Amorzhu\Weather\Exceptions\HttpException;
use Amorzhu\Weather\Exceptions\InvalidArgumentException;
use GuzzleHttp\Exception\GuzzleException;

class Weather
{
    protected string $key = '';

    protected array $guzzleOptions = [];

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getHttpClient(): Client
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        // 1. 对 `$format` 与 `$type` 参数进行检查，不在范围内的抛出异常。
        if (!in_array(strtolower($format), ['xml', 'json'])) {
            throw new InvalidArgumentException('Invalid response format: '.$format);
        }

        if (!in_array(strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): '.$type);
        }

        // 2. 封装 query 参数，并对空值进行过滤。
        $query = array_filter([
            'key' => $this->key,
            'city' => $city,
            'output' => \strtolower($format),
            'extensions' =>  \strtolower($type),
        ]);

        // 3. 调用 getHttpClient 获取实例，并调用该实例的 `get` 方法，
        // 传递参数为两个：$url、['query' => $query]，
        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();

            // 4. 返回值根据 $format 返回不同的格式，
            // 当 $format 为 json 时，返回数组格式，否则为 xml。
            return 'json' === $format ? json_decode($response, true, 512, JSON_THROW_ON_ERROR) : $response;
        } catch (\Exception | GuzzleException $e) {
            // 5. 当调用出现异常时捕获并抛出，消息为捕获到的异常消息，
            // 并将调用异常作为 $previousException 传入。
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}