<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraint as Assert;

class BlogDTO {

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=5)
     */
    public string $title;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=10)
     */
    public string $content;

    /**
     * @Assert\NotBlank
     * @Assert\DateTime
     */
    public \DateTime $date;

    public function __construct(
        string $title,
        string $content,
        \DateTime $date
    ) {
        $this->title = $title;
        $this->content = $content;
        $this->date = $date;
    }
}