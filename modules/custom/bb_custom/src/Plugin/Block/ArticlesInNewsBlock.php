<?php

    namespace Drupal\bb_custom\Plugin\Block;

    use Drupal\Core\Block\BlockBase;
    use Drupal\Core\Form\FormInterface;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\file\Entity\File;
    use Drupal\image\Entity\ImageStyle;

   /**
     * Provides a 'Articles in news' block.
     *
     * @Block(
     *   id = "articles_in_news_custom_block",
     *   admin_label = @Translation("Articles In News Block"),
     *   category = @Translation("BB App Module Blocks")
     * )
    */
    class ArticlesInNewsBlock extends BlockBase {
      
     /**
      * {@inheritdoc}
     */
     public function build() {
      $query = \Drupal::entityQuery('node')
        //->addTag('debug')
        ->condition('type', 'article')
        ->condition('field_tags', 17)
        ->condition('field_article_categories', 2)
        ->sort('nid', 'ASC')
        ->range(0, 2);
      $nids = $query->execute();
      //kint($nids); 
      $database = \Drupal::database();
      $nodeYoutubeUrls = array();
      $sql = "SELECT field_youtube_video_url_value,entity_id FROM node__field_youtube_video_url WHERE entity_id IN (:ids[])";
      $query = $database->query($sql, [':ids[]' => $nids]);
      $res = $query->fetchAll();

      if(count($res)>=1){
        for ($i = 0; $i <= (count($res)-1); $i++){
          $nodeYoutubeUrls[$res[$i]->entity_id]= $res[$i]->field_youtube_video_url_value;
        }
      }

      $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
      $nodeDataArr = array();
      foreach($nodes as $node){
          $nodeDataArr[$node->id()]['title'] = $node->getTitle();
          $nodeDataArr[$node->id()]['youtubeurl'] = $this->convertYoutube($nodeYoutubeUrls[$node->id()]);
      }
      //kint($nodeDataArr);  
      return [
          '#theme' => 'articles_in_news',
          '#data' => $nodeDataArr,
      ];
     }
     
    public function convertYoutube($string) {
        return preg_replace(
            "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
            "https://www.youtube.com/embed/$2",
            $string
        );
    }

   }