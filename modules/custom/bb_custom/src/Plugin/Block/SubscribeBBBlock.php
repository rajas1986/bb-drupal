<?php

    namespace Drupal\bb_custom\Plugin\Block;

    use Drupal\Core\Block\BlockBase;

   /**
     * Provides a 'Subscribe BB' block.
     *
     * @Block(
     *   id = "subscribe_bb_block",
     *   admin_label = @Translation("Subscribe BB Block"),
     *   category = @Translation("BB App Module Blocks")
     * )
    */
    class SubscribeBBBlock extends BlockBase {

     /**
      * {@inheritdoc}
     */
     public function build() {

       $form = \Drupal::formBuilder()->getForm('Drupal\bb_custom\Form\SubscribeForm');

       return $form;
     }
   }