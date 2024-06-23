<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraint as Assert;

class BlogDTO {

    /*
     * @Assert\NotBlank
     */
    public string $title;

    /*
     * @Assert\NotBlank
     */
    public string $content;

    /*
     * @Assert\NotBlank
     */
    public \DateTime $date;

    public function __construct(string $title, string $content, \DateTime $date) {
        $this->title = $title;
        $this->content = $content;
        $this->date = $date;
    }
}