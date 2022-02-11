<?php
namespace Drupal\days_difference\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class DaysDifferenceForm.
 *
 * @package Drupal\days_difference\Form
 */
class DaysDifferenceForm extends FormBase {
/**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'days_difference_form';
  }

   /** 
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'days_difference.settings';

  
  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);
    $conn = Database::getConnection();
    $format = 'Y-m-d';

    $form['first_date'] = array(
      '#type' => 'date',
      '#title' => t('First Date'),
      '#required' => TRUE,
      '#default_value' => $config->get('first_date'),
      '#prefix' => "Days: ".$config->get('result'),// Add markup before form item
      '#date_format' => $format,
      );

    $form['second_date'] = array(
      '#type' => 'date',
      '#title' => t('Second Date'),
      '#required' => TRUE,
      '#default_value' => $config->get('second_date'),
      '#date_format' => $format,
      );

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'save',
    ];
    return $form;
  }

  /**
    * {@inheritdoc}
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {
         $first_date = $form_state->getValue('first_date');
         $second_date = $form_state->getValue('second_date');
     
         if($first_date > $second_date){
            $form_state->setErrorByName('first_date', $this->t('Second date should be greater then First date'));
         }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $firstDate = $form_state->getValue('first_date');
    $secondDate = $form_state->getValue('second_date');
    $this->configFactory->getEditable(static::SETTINGS)
    ->set('first_date', $firstDate)
    ->set('second_date',$secondDate)
    ->set('result', $this->dayFun($firstDate, $secondDate))
    ->save();
     $this->dayFun($firstDate, $secondDate); 
  }

  /**
   * Calculating date
   */
  public function dayFun($first_date, $second_date)
  {
    $firstDate = explode("-",$first_date);
    $secondDate = explode("-",$second_date);
    $monthsArray = [0,31, 28,31,30,31,30,31,31,30,31,30,31];
    $days = 0;
    $fy =  (int)$firstDate[0];
    $fm =  (int)$firstDate[1];
    $fd =   (int)$firstDate[2];

    $sy =  (int)$secondDate[0];
    $sm =   (int)$secondDate[1];
    $sd =   (int)$secondDate[2];
 

 $flag = true;
 $daycount =0;
    while($fy != $sy +1 ){
      while($fm !=  13){
        $monthLength =  $monthsArray[$fm];
        if($fy % 4 == 0 && $monthLength == 28){
          $monthLength =29;
        }
        while($fd !=  $monthLength +1){
        
          if($fd == $sd && $fm == $sm && $fy == $sy){
            $flag =false;
            break;
          }
          $daycount++;
          $fd++;
        }
        if($flag == false)
          break;
        
        $fd = 1;
        $fm++;
       
      }
      if($flag == false)
        break;
      $fm = 1;
      $fy++;
    }
 
  return $daycount;

   
    // $monthsArray = [31, 28,31,30,31,30,31,31,30,31,30,31];
    // for($i = $firstDate[0]; $i <= $secondDate[0]; $i++){
    //   if($days == 0){
    //     $month  = (int)$firstDate[1];
    //   }
    //   else{
    //     $month = 1;
    //   }
    //   if($i == $secondDate[0]){
    //     $monthlength = (int)$firstDate[1];
    //   }
    //   else{
    //     $monthlength = 12;
    //   }
    //   while($month <=$monthlength){
    //     if($days == 0){
    //       $day  = (int)$firstDate[2];
    //     }
    //     else{
    //       $day = 1;
    //     }
    //     $leapYear = false;
    //     $dayCount = $monthsArray[$month-1];
    //     if($i % 4 == 0 && $dayCount==28){
    //       $leapYear = true;
    //     }
    //     if($leapYear == true){
    //       $dayCount = 29;
    //     }
    //     if($days == 0){
    //       $dayCount = (int)$secondDate[2];
    //     } 
    //     while($day <= $dayCount){
    //       $day ++;
    //       $days++;
    //     }
    //     $month ++;
    //   }
    // }
    //   return $days;
  }  
}
