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
        if (count($this->topics) === 0) {
            echo "<p>There are no topics here. Consider starting one!</p>";
        }
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
                        echo $topic->getDescriptionHTML();
                        ?>
                    </span>
                </li>
            <?php } ?>
        </ul>
    <?php }
}