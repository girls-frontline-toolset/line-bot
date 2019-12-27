<?php


class CarouselList
{

    public $altText = "";
    public $CarouselBeanList = array();

    /**
     * CarouselList constructor.
     * @param string $altText
     */
    public function __construct($altText)
    {
        $this->altText = $altText;
    }

    public function AddCarousel($imageUrl, $isHeavy, $url){
        $bean = new CarouselBean($imageUrl, $isHeavy, $url);
        array_push($this->CarouselBeanList, $bean);
    }

}

class CarouselBean{

    public $imageUrl = "";
    public $isHeavy = false;
    public $url = "";

    /**
     * CarouselBean constructor.
     * @param string $imageUrl
     * @param bool $isHeavy
     * @param string $url
     */
    public function __construct($imageUrl, $isHeavy, $url)
    {
        $this->imageUrl = $imageUrl;
        $this->isHeavy = $isHeavy;
        $this->url = $url;
    }

}
