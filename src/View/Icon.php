<?php

namespace Vada\View;

// TODO: the icon element currently also contains a heading. that should be moved back into the claim card view.
enum Icon {

    case Thesis;
    case Contested;
    case Flag;
    case Support;
    case Rival;

    private function getName() {
        return match($this) {
            Icon::Contested => "Contested",
            Icon::Flag => "Flag",
            Icon::Support => "Support",
            Icon::Rival => "Rivals",
            Icon::Thesis => "Thesis",
        };
    }

    private function getClassName() {
        return match($this) {
            Icon::Contested => "contested",
            Icon::Flag => "flag",
            Icon::Support => "support",
            Icon::Rival => "rivals",
            Icon::Thesis => "thesis"
        };
    }
    private function getFileName() {
        return match($this) {
            Icon::Contested => "alert.svg",
            Icon::Flag => "flag.svg",
            Icon::Support => "thumbs-up.svg",
            Icon::Rival => "minimize-2.svg",
            Icon::Thesis => null,
        };
    }

    public function getIconElement() {
        $file = $this->getFileName();
        if (isset($file)) {
            $res = file_get_contents(__DIR__ . "/../../assets/svg/$file");
            return $res;
        }
    }
    public function getHeading(string $tagName) {
        $name = $this->getName();
        $class = $this->getClassName();
        $imgEl = $this->getIconElement();
        return "<$tagName class='heading heading--$class'>$name $imgEl</$tagName>";
    }
}