<?php

require_once 'old/old_model_page.php';
require_once 'new/model_page_insert.php';
require_once 'new/model_page_update.php';
require_once 'new/model_page_delete.php';

class PageModel extends BaseModel {

    use OldModelPageTrait;
    use ModelPageInsertTrait;
    use ModelPageUpdateTrait;
    use ModelPageDeleteTrait;
    
    // ===============================================================

    // protected string $table = 'pages';


    // ── Settings / CMS ────────────────────────────────────────────────────────
    public function getSettingsByPage(string $page): array {
        global $config;
        $locale = $config['app']['lang'] ?? 'fa';

        return $this->db->query("SELECT * FROM sor_settings 
            LEFT OUTER JOIN translations ON sor_settings.setting_id = translations.table_id 
            WHERE locale=:locale AND translations.table_name=:table_name AND page=:page
        ", array(
            'locale'     => $locale,
            'table_name' => 'sor_settings',
            'page'       => $page
        ));
    }

  public function getHomeData():            array { return $this->getSettingsByPage('home');       }
  public function getHeaderData():          array { return $this->getSettingsByPage('header');     }
  public function getFooterData():          array { return $this->getSettingsByPage('footer');     }
  public function getAboutUsContent():      array { return $this->getSettingsByPage('about_us');   }
  public function getContactUsCategories(): array { return $this->getSettingsByPage('contact_us'); }


  // ── Academy ───────────────────────────────────────────────────────────────

  // public function getAcademyById(int $academyId): ?array {
  //   return $this->db->first(
  //     "SELECT * FROM sor_academy WHERE academy_id = :id",
  //     ['id' => $academyId]
  //   );
  // }


  // ── Users ─────────────────────────────────────────────────────────────────

  // public function getUserById(int $userId): ?array {
  //   return $this->db->first(
  //     "SELECT * FROM sor_users WHERE user_id = :id",
  //     ['id' => $userId]
  //   );
  // }


  // ── Contacts ─────────────────────────────────────────────────────────────

  // public function addContact(
  //   string $authorEmail,
  //   string $author,
  //   int    $parent,
  //   int    $userId,
  //   int    $postId,
  //   string $type,
  //   string $content,
  //   int    $receiverUserId,
  //   string $agent
  // ): int {
  //   return $this->db->insert(
  //     "INSERT INTO sor_contacts
  //       (user_id, author_email, author, post_id, content, parent, receiver_user_id, agent, type)
  //      VALUES
  //       (:user_id, :author_email, :author, :post_id, :content, :parent, :receiver_user_id, :agent, :type)",
  //     [
  //       'user_id'          => $userId,
  //       'author_email'     => $authorEmail,
  //       'author'           => $author,
  //       'post_id'          => $postId,
  //       'content'          => $content,
  //       'parent'           => $parent,
  //       'receiver_user_id' => $receiverUserId,
  //       'agent'            => $agent,
  //       'type'             => $type,
  //     ]
  //   );
  // }


  // ── Pages ─────────────────────────────────────────────────────────────────

  // public function findBySlug(string $slug): ?array {
  //   return $this->db->first(
  //     "SELECT * FROM `{$this->table}` WHERE slug = :slug AND active = 1 LIMIT 1",
  //     ['slug' => $slug]
  //   );
  // }

}
