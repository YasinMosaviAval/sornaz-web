const fs = require('node:fs');
const { execFileSync } = require('node:child_process');
exports.renderPanelView = (name, locale = 'fa') => execFileSync('php', ['scripts/panel-table-view-fixture.php', name, locale], { encoding: 'utf8' });
exports.tableRuntime = () => {
  const template = fs.readFileSync('Modules/Analytics/Resources/Views/sections/panel-table-template.php', 'utf8');
  const markup = template.match(/<script type="application\/json" id="adminPanelTableMarkup">[\s\S]*?<\/script>/)[0];
  return markup + '<script>' + fs.readFileSync('assets/Analytics/js/admin-ui-templates.js', 'utf8') + '</script>';
};
