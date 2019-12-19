<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191219214217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE attend_survey (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, survey_id INT NOT NULL, INDEX IDX_8B6846E1A76ED395 (user_id), INDEX IDX_8B6846E1B3FE509D (survey_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attend_survey_answer_option (attend_survey_id INT NOT NULL, answer_option_id INT NOT NULL, INDEX IDX_F2781DDF2B2B7EC5 (attend_survey_id), INDEX IDX_F2781DDF9A3BC2B9 (answer_option_id), PRIMARY KEY(attend_survey_id, answer_option_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attend_survey ADD CONSTRAINT FK_8B6846E1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE attend_survey ADD CONSTRAINT FK_8B6846E1B3FE509D FOREIGN KEY (survey_id) REFERENCES survey (id)');
        $this->addSql('ALTER TABLE attend_survey_answer_option ADD CONSTRAINT FK_F2781DDF2B2B7EC5 FOREIGN KEY (attend_survey_id) REFERENCES attend_survey (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attend_survey_answer_option ADD CONSTRAINT FK_F2781DDF9A3BC2B9 FOREIGN KEY (answer_option_id) REFERENCES answer_option (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE attend_survey_answer_option DROP FOREIGN KEY FK_F2781DDF2B2B7EC5');
        $this->addSql('DROP TABLE attend_survey');
        $this->addSql('DROP TABLE attend_survey_answer_option');
    }
}
