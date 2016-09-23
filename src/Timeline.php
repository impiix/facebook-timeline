<?php

namespace App;

use Facebook\Facebook;

class Timeline
{
    /**
     * @var Facebook
     */
    protected $facebook;

    /**
     * Timeline constructor.
     * @param Facebook $facebook
     */
    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * @param string $token
     *
     * @return array
     */
    public function generate($token)
    {
        $data = $this->callFb($token);
        $newData = $this->callFb($token, "&type=uploaded");
        $data = array_merge($data, $newData);

        $results = [];
        foreach ($data as $element) {
            $results[$element['id']] = [
                'src' => $element['images'][0]['source'],
                'ratio' => $element['images'][0]['width'] / $element['images'][0]['height'],
                'created' => new \DateTime($element['created_time'])
            ];
        }

        usort($results, function ($a, $b) {return $a['created'] < $b['created'];});

        return $results;
    }

    /**
     * @param string $token
     * @param string $append
     *
     * @return array
     */
    protected function callFb($token, $append = "") {
        $response = $this->facebook->get("/me/photos?fields=images,created_time" . $append, $token);
        $body = $response->getDecodedBody();
        $subData = [];
        if (isset($body['paging']['next'])) {
            $subData = $this->callFb($token, $append . "&after=" . $body['paging']['cursors']['after']);
        }
        return array_merge($body['data'], $subData);
    }
}
