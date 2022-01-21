<?php

    namespace Drupal\bb_custom\Plugin\Block;

    use Drupal\Core\Block\BlockBase;
    use Drupal\Core\Form\FormInterface;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\file\Entity\File;

   /**
     * Provides a 'Related Articles' block.
     *
     * @Block(
     *   id = "related_articles_custom_block",
     *   admin_label = @Translation("Related Articles Block"),
     *   category = @Translation("BB App Module Blocks")
     * )
    */
    class RelatedArticlesBlock extends BlockBase {
      
     /**
      * {@inheritdoc}
     */
     public function build() {
      $nodeDataArr = array();
      $node = \Drupal::routeMatch()->getParameter('node');
      if ($node instanceof \Drupal\node\NodeInterface) {
        $currentnid = $node->id();
        //dump($node);  //comes from symfony var dumper enabled in devel settings page
        //kint($node->field_tags);
        if(isset($node->field_tags)){
          $termids = $node->get('field_tags')->getValue();
          $termArr = array();
          if(!empty($termids)){
            foreach($termids as $termid){
              array_push($termArr, $termid['target_id']); 
            }  
          }
          
          if(isset($termArr) && !empty($termArr)){
            $query = \Drupal::entityQuery('node')
            ->condition('type', 'article')
            ->condition('field_tags', $termArr, 'IN')
            ->sort('nid', 'ASC')
            ->range(0, 10);
            $nids = $query->execute();
            $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
            foreach($nodes as $node){
              if($node->id()!=$currentnid){
                $nodeDataArr[$node->id()]['title'] = $node->getTitle();
                $nodeDataArr[$node->id()]['short_description'] = $node->get("field_short_description")->getValue();
              }
            }
          } 
        }
      }
      return [
          '#theme' => 'library_related_articles',
          '#data' => $nodeDataArr,
      ];
     }
     
   }