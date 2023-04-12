<?php

namespace Vada\View;

use Vada\Model\Topic;

class TopicsList
{
    /** @var Topic[]  */
    private array $topics;

    /**
     * @param Topic[] topics
     */
    public function __construct(array $topics)
    {
        $this->topics = $topics;
    }

    public function render()
    {
        ?>
        <ul class="topics-list">
            <?php foreach ($this->topics as $topic) { ?>
                <li class="topics-list__item">
                    <a class="topics-list__item__link"
                        href="<?php echo $topic->getURL(); ?>">
                        <?php echo htmlspecialchars($topic->name); ?>
                    </a>
                    <span class="topics-list__item__description">
                        <?php
                        echo htmlspecialchars($topic->description ?? "(no description)");
                        ?>
                    </span>
                </li>
            <?php } ?>
        </ul>
    <?php }
}