<?php
namespace AdminBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DigestCommand extends ContainerAwareCommand
{
    protected $sendTo; # doctor # test@test@test.ru
    protected $subject = 'Приглашаем посетить II Съезд Евразийской Ассоциации Терапевтов';
    protected $template = 'AppBundle:Delivery:test.html.twig';
    protected $deliveryName = 'Delivery-1';

    protected function configure()
    {
        $this->setName('delivery:mail')
            ->addArgument('sendTo', InputArgument::IS_ARRAY, 'sendTo argument');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        set_time_limit(0); # снимаем ограничение времени выполнения скрипта (в safe-mode не работает)

        $templating = $this->getContainer()->get('templating');


        $sendTo = $input->getArgument('sendTo'); # определяем, кому отправляем

        if (empty($sendTo) || count($sendTo) != 1) {
            $output->writeln('ADD ARGUMENT: test@test.ru | doctor');
            return;
        }

        $this->sendTo = $sendTo[0];
        $em           = $this->getContainer()->get('doctrine')->getManager();
        $pdo          = $em->getConnection();

        # тестовый режим
        if (strpos($this->sendTo, '@') !== false) {

            $html =  $templating->render($this->template, ['email' => $this->sendTo]);
            $email = $this->sendTo;
            $to    = $this->sendTo;
            $error = $this->send($email, $to, $html, $this->subject, true);
            $output->writeln($error);
            $output->writeln("... sent to <{$this->sendTo}>");

            return;
        }

        include_once __DIR__.'/Armeni2.php';
        $doctors = $emails;

        # рассылка по 100 пользователям за цикл
        for ($i = 0, $c = count($doctors); $i < $c; $i++) {
            $html = $templating->render($this->template, array('email' => $doctors[$i]));

            $email = $doctors[$i];
            $to    = $doctors[$i];

            $error = $this->send($email, $to, $html, $this->subject);
            $output->writeln($error);
            $output->writeln($email);
            if ($i && ($i % 30) == 0) {
                sleep(60);
            }
        }

    }

    public function send($email, $to, $body, $subject, $local = false)
    {
        $mail = new \PHPMailer();

        $mail->isSMTP();
        $mail->isHTML(true);
        $mail->SMTPDebug = 0;
        $mail->SMTPSecure = 'ssl';
        $mail->CharSet  = 'UTF-8';
        $mail->From     = 'mailer@euat.ru';
        $mail->FromName = 'Евразийская Ассоциация Терапевтов';
        $mail->Host     = 'smtp.yandex.ru';
        $mail->Username = 'mailer@euat.ru';
        $mail->Password = 'K9nYoTW4GW';
        $mail->SMTPAuth = true;
        $mail->Port     = 465;
        $mail->Subject  = $subject;
        $mail->Body     = $body;
        $mail->addAddress($email, $to);
        $mail->addCustomHeader('Precedence', 'bulk');
        $mail->addCustomHeader('List-Unsubscribe', "<http://euat.ru/unfollow?email=$email>");

        return $mail->send() ? null : $mail->ErrorInfo;
    }
}
