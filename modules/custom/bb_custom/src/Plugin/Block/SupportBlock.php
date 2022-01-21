<?php

    namespace Drupal\bb_custom\Plugin\Block;

    use Drupal\Core\Block\BlockBase;
    use Drupal\Core\Form\FormInterface;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\file\Entity\File;
    use Drupal\image\Entity\ImageStyle;

   /**
     * Provides a 'Support' block.
     *
     * @Block(
     *   id = "support_custom_block",
     *   admin_label = @Translation("Support Block"),
     *   category = @Translation("BB App Module Blocks")
     * )
    */
    class SupportBlock extends BlockBase {
      
     /**
      * {@inheritdoc}
     */
     public function build() {
      $query = \Drupal::entityQuery('node')
        //->addTag('debug')
        ->condition('type', 'support_pages')
        ->sort('nid', 'ASC');

      $nids = $query->execute();
      //kint($nids); 
      $database = \Drupal::database();
      $term_data = array();
      $vid = 'support_category';
      $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
      $aliasManager = \Drupal::service('path_alias.manager');
      foreach ($terms as $term) {
       $term_data[$term->tid]['name'] = $term->name;
       $term_data[$term->tid]['path'] = $aliasManager->getAliasByPath('/taxonomy/term/'.$term->tid);
      }

      $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
      $nodeDataArr = array();
      foreach($nodes as $node){
          //kint($node); 
          if($node->get('field_sub_page_of')->target_id==null){
            $nodeDataArr[$term_data[$node->get('field_support_category')->target_id]['name']][$node->id()]['title'] = $node->getTitle();
            $nodeDataArr[$term_data[$node->get('field_support_category')->target_id]['name']][$node->id()]['path'] = \Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$node->id());
          }
          
      }
      //kint($nodeDataArr);  
      return [
          '#theme' => 'support_block',
          '#data' => ['nodes'=>$nodeDataArr],
          '#attached' => [
            'library' => [
              'bb_custom/easysearch_assets_attachments',
            ],
          ],
      ];
     }

   }