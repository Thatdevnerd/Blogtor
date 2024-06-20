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
    public int $date;

    public function __construct(string $title, string $content, int $date) {
        $this->title = $title;
        $this->content = $content;
        $this->date = $date;
    }
}