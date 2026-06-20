<?php

class View {

  /**
   * صفحه کامل رو داخل theme/default.php رندر می‌کنه
   * Usage: View::render('/page/home', 'عنوان صفحه', ['key' => 'val']);
   */
  public static function render(string $filePath, string $pageTitle = null, array $data = []): void {
    if ($pageTitle !== null) {
      setPageTitle($pageTitle);
    }
    $data['settings'] = SettingsController::get_settings();
    $data['header'] = setIndexforDataArray(SettingsController::get_header_data(), 'variable_name');
    $data['footer'] = setIndexforDataArray(SettingsController::get_footer_data(), 'variable_name');
    $data['user-permissions'] = AdminController::get_user_permissions();
    $data['menu-permissions'] = AdminController::get_menu_with_permissions();


    extract($data);

    $viewFile = getcwd() . '/mvc/view' . $filePath . '.php';
    if (!file_exists($viewFile)) {
      throw new RuntimeException("View not found: $filePath");
    }

    ob_start();
    require $viewFile;
    $content = ob_get_clean(); // توسط theme/default.php استفاده میشه

    require getcwd() . '/theme/default.php';
  }


  /**
   * partial view بدون theme — یک‌بار لود میشه (require_once)
   * Usage: View::partial('/message/success', ['message' => 'ذخیره شد']);
   *        $html = View::partial('/card/item', $data, return: true);
   */
  public static function partial(string $filePath, array $data = [], bool $return = false): string|null {
    $data['settings'] = SettingsController::get_settings();
    $data['header'] = setIndexforDataArray(SettingsController::get_header_data(), 'variable_name');
    $data['footer'] = setIndexforDataArray(SettingsController::get_footer_data(), 'variable_name');
    $data['user-permissions'] = AdminController::get_user_permissions();
    $data['menu-permissions'] = AdminController::get_menu_with_permissions();
    
    extract($data);

    $viewFile = getcwd() . '/mvc/view' . $filePath . '.php';
    if (!file_exists($viewFile)) {
      throw new RuntimeException("Partial view not found: $filePath");
    }

    if ($return) {
      ob_start();
      require_once $viewFile;
      return ob_get_clean();
    }

    require_once $viewFile;
    return null;
  }


  /**
   * partial view قابل استفاده چندبار — مثلاً داخل foreach
   * Usage:
   *   foreach ($items as $item) {
   *     View::each('/card/item', ['item' => $item]);
   *   }
   */
  public static function each(string $filePath, array $data = [], bool $return = false): string|null {
    $data['settings'] = SettingsController::get_settings();
    $data['header'] = setIndexforDataArray(SettingsController::get_header_data(), 'variable_name');
    $data['footer'] = setIndexforDataArray(SettingsController::get_footer_data(), 'variable_name');
    $data['user-permissions'] = AdminController::get_user_permissions();
    $data['menu-permissions'] = AdminController::get_menu_with_permissions();
    extract($data);

    $viewFile = getcwd() . '/mvc/view' . $filePath . '.php';
    if (!file_exists($viewFile)) {
      throw new RuntimeException("View not found: $filePath");
    }

    if ($return) {
      ob_start();
      require $viewFile;
      return ob_get_clean();
    }

    require $viewFile;
    return null;
  }


  // ── Backward Compatibility ────────────────────────────────────────────────

  /** @deprecated از View::partial استفاده کن */
  // public static function renderPartial(string $filePath, array $data = [], bool $return = false): string|null {
  //   return self::partial($filePath, $data, $return);
  // }

  /** @deprecated از View::each استفاده کن */
  // public static function renderPartialManyTimes(string $filePath, array $data = [], bool $return = false): string|null {
  //   return self::each($filePath, $data, $return);
  // }

}
