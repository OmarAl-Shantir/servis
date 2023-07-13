<?php
/**
 *
 */
class MailController extends Mail
{
  public function add_template($subject, $filename){
    return $this->addTemplate($subject, $filename);
  }

  public function update_template($id_mail, $subject, $filename){
    return $this->updateTemplate($id_mail, $subject, $filename);
  }
}

?>
