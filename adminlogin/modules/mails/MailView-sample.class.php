<?php


  /**
   *
   */
  class MailView extends Mail
  {
    public function get_all_records($limit) {
      return $this->getAllRecords($limit);
    }

    public function get_data($id_mail){
      return $this->getData($id_mail);
    }

    public function generate_mail($id_mail, $user_data = NULL){
      $data = $this->get_data($id_mail)[0];
      $mailContent = file_get_contents(__DIR__.'/templates/'.$data['filename'].".html");
      foreach (DEFINED_CONSTANTS as $key => $value){
          $mailContent = str_replace(strtolower("{".$key."}"), $value, $mailContent);
      }
      if (!empty($user_data)){
        foreach ($user_data as $key => $value){
            $mailContent = str_replace($key, $value, $mailContent);
        }
      }
      return $mailContent;
    }

    public function send_mail($user, $data){
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        try {
      //Server settings
          //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
          $mail->isSMTP();                                            //Send using SMTP
          $mail->Host       = '';                     //Set the SMTP server to send through
          $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
          $mail->Username   = '';                     //SMTP username
          $mail->Password   = '';                               //SMTP password
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
          $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

          //Recipients
          $mail->setFrom('', '');     //send from, name
          $mail->addAddress($user['email'], $user['fullname']);     //Add a recipient
          //$mail->addAddress('ellen@example.com');               //Name is optional
          $mail->addReplyTo('', '');    //reply to, name
          //$mail->addCC('cc@example.com');
          //$mail->addBCC('bcc@example.com');

          //Attachments
          //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
          //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

          //Content
          $mail->isHTML(true);                                  //Set email format to HTML
          $mail->Subject = $data['subject'];
          $mail->Body    = $data['content'];
          //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

          $mail->send();
          return 'Správa bola odoslaná';
        } catch (Exception $e) {
          return "Správa nebola odoslaná: {$mail->ErrorInfo}";
        }
    }
  }

?>
