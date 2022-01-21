<?php

    namespace Drupal\bb_custom\Plugin\Block;

    use Drupal\Core\Block\BlockBase;
    use Drupal\Core\Form\FormInterface;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\file\Entity\File;
    use Drupal\image\Entity\ImageStyle;

   /**
     * Provides a 'Category Articles Listing' block.
     *
     * @Block(
     *   id = "category_articles_listing_custom_block",
     *   admin_label = @Translation("Category Articles Listing Block"),
     *   category = @Translation("BB App Module Blocks")
     * )
    */
    class CategoryArticlesListingBlock extends BlockBase {
      
     /**
      * {@inheritdoc}
     */
     public function build() {
      $term = \Drupal::routeMatch()->getParameter('taxonomy_term');
      $currentTermId = $term->id();
      $termArr = array();
      array_push($termArr, $currentTermId);
      $query = \Drupal::entityQuery('node')
        //->addTag('debug')
        ->condition('type', 'article')
        ->condition('field_tags', $termArr, 'IN')
        ->condition('field_article_categories', 3)
        ->sort('nid', 'ASC')
        ->range(0, 10);
      $nids = $query->execute();
      //echo "<pre/>"; print_r($nids); exit;
      $database = \Drupal::database();
      $sql = "SELECT SUM(view_count) as viewcount,nid  FROM custom_node_views WHERE nid IN (:ids[]) GROUP BY nid";
      $result = $database->query($sql, [':ids[]' => array_values($nids)]);
      $viewCountArr = array();
      if ($result) {
        while ($row = $result->fetchAssoc()) {
          $viewCountArr[$row['nid']] = $row['viewcount'];
        }
      }

      $sql = "SELECT votes,nid  FROM custom_vote_mapping WHERE nid IN (:ids[])";
      $result = $database->query($sql, [':ids[]' => array_values($nids)]);
      $voteCountArr = array();
      if ($result) {
        while ($row = $result->fetchAssoc()) {
          $voteCountArr[$row['nid']] = $row['votes'];
        }
      }

      $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
      $nodeDataArr = array();
      foreach($nodes as $node){
          $nodeDataArr[$node->id()]['title'] = $node->getTitle();
          $nodeDataArr[$node->id()]['image'] = ImageStyle::load('category_articles_listing')->buildUrl($node->field_image->entity->getFileUri());
          $nodeDataArr[$node->id()]['username'] = $node->getOwner()->getDisplayName();
          $nodeDataArr[$node->id()]['viewcount'] = $viewCountArr[$node->id()];
          $nodeDataArr[$node->id()]['votecount'] = $voteCountArr[$node->id()];
          //category_articles_listing_image_style
      }
      //echo "<pre>"; print_r($nodeDataArr); exit;
      
      return [
          '#theme' => 'category_articles_listing',
          '#data' => $nodeDataArr,
      ];
     }
     
   }