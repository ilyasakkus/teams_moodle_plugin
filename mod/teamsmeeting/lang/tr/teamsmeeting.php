<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Turkish language strings for mod_teamsmeeting.
 *
 * @package     mod_teamsmeeting
 * @copyright   2026 Custom Development
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Genel.
$string['modulename'] = 'Teams Toplantısı';
$string['modulenameplural'] = 'Teams Toplantıları';
$string['modulename_help'] = 'Teams Toplantısı etkinliği, eğitmenlerin Moodle üzerinden doğrudan Microsoft Teams toplantıları planlamasına olanak tanır. Diğer eklentilerin aksine, iframe yerine popup pencere kullanarak üniversite/kurumsal ağlarda Chrome dahil TÜM tarayıcılarda çalışır (PNA/CORS uyumlu).';
$string['pluginname'] = 'Teams Toplantısı';
$string['pluginadministration'] = 'Teams Toplantısı yönetimi';
$string['teamsmeeting:addinstance'] = 'Yeni bir Teams Toplantısı ekle';
$string['teamsmeeting:view'] = 'Teams Toplantısını görüntüle';
$string['teamsmeeting:joinmeeting'] = 'Teams Toplantısına katıl';
$string['teamsmeeting:managemeeting'] = 'Teams Toplantısını yönet';

// Ayarlar.
$string['meetingappsettings'] = 'Toplantı Uygulaması Ayarları';
$string['meetingappsettings_desc'] = 'Teams toplantıları oluşturmak için kullanılan harici toplantı uygulamasını yapılandırın. Uygulama, Chrome PNA ve üçüncü taraf çerez kısıtlamalarını aşmak için popup pencerede (iframe değil) açılır.';
$string['meetingappurl'] = 'Toplantı Uygulaması URL\'si';
$string['meetingappurl_desc'] = 'Teams toplantı oluşturma uygulamasının URL\'si. Varsayılan olarak Azure\'da barındırılan Enovation uygulamasıdır. Bu uygulamayı kendi sunucunuzda da barındırabilirsiniz. <br><strong>Varsayılan:</strong> <code>https://enomsteams.z16.web.core.windows.net</code><br><strong>Alternatif:</strong> <code>https://www.enovation.ie/msteams/</code>';
$string['displaysettings'] = 'Görüntüleme Ayarları';
$string['displaysettings_desc'] = 'Toplantı bağlantılarının öğrencilere nasıl gösterileceği.';
$string['openinnewtab'] = 'Yeni Sekmede Aç';
$string['openinnewtab_desc'] = 'Toplantı bağlantılarını her zaman yeni tarayıcı sekmesinde aç (en iyi uyumluluk için önerilir).';
$string['pnainfo'] = 'PNA/CORS Uyumluluğu';
$string['pnainfo_desc'] = 'Bu eklenti, toplantı oluşturmak için iframe yerine <strong>popup pencere</strong> yaklaşımı kullanır. Bu sayede Chrome\'un Private Network Access (PNA) kısıtlamalarını ve üçüncü taraf çerez engellemesini aşar. Üniversite/kurumsal ağlarda diğer Teams eklentilerinin başarısız olmasına neden olan sorunlar ortadan kalkar.';

// Form.
$string['meetingname'] = 'Toplantı Adı';
$string['meetinglink'] = 'Toplantı Bağlantısı';
$string['meetingschedule'] = 'Toplantı Programı';
$string['meetingstart'] = 'Başlangıç Zamanı';
$string['meetingend'] = 'Bitiş Zamanı';
$string['createmeetingbtn'] = 'Teams Toplantısı Oluştur';
$string['createmeetinghelp'] = 'Teams toplantı uygulamasını popup pencerede açar. Microsoft hesabınızla oturum açın, toplantı oluşturun, URL otomatik yakalanacaktır.';
$string['enterurlmanually'] = 'Veya toplantı URL\'sini manuel girin...';
$string['useurl'] = 'Bu URL\'yi Kullan';
$string['changeurl'] = 'Değiştir';
$string['urlset'] = 'Toplantı URL\'si ayarlandı!';

// Popup sayfası.
$string['waitingforurl'] = 'Toplantı URL\'si bekleniyor...';
$string['urlcaptured'] = 'Toplantı URL\'si yakalandı! Bu pencere otomatik kapanacak.';
$string['urlcapturedshort'] = 'URL Yakalandı!';
$string['invalidteamsurl'] = 'Lütfen geçerli bir Microsoft Teams toplantı URL\'si girin (teams.microsoft.com veya teams.live.com içermelidir).';
$string['errorendbeforestart'] = 'Toplantı bitiş zamanı başlangıç zamanından sonra olmalıdır.';

// Görüntüleme sayfası.
$string['joinmeeting'] = 'Teams Toplantısına Katıl';
$string['copylink'] = 'Bağlantıyı Kopyala';
$string['copied'] = 'Kopyalandı!';
$string['joinhelp'] = 'Düğmeye tıklamak Microsoft Teams\'i yeni bir sekmede açar. Web uygulaması veya masaüstü uygulaması üzerinden katılabilirsiniz. Tüm tarayıcılarda çalışır.';
$string['nomeetingurl'] = 'Toplantı bağlantısı henüz ayarlanmadı. Lütfen eğitmeninizle iletişime geçin.';
$string['managemeeting'] = 'Toplantı Yönetimi';
$string['editmeetingurl'] = 'Toplantı URL\'sini Düzenle';
$string['nourlyet'] = 'Henüz toplantı URL\'si ayarlanmamış. Oluşturmak veya yapıştırmak için "Toplantı URL\'sini Düzenle" butonuna tıklayın.';

// Durum.
$string['statusupcoming'] = 'Yaklaşan';
$string['statusactive'] = 'Devam Ediyor';
$string['statusended'] = 'Sona Erdi';

// İndeks sayfası.
$string['nomeetings'] = 'Bu derste Teams Toplantısı bulunmamaktadır.';

// Sıfırlama.
$string['resetmeetings'] = 'Tüm toplantı bağlantılarını sıfırla';

// Gizlilik.
$string['privacy:metadata'] = 'Teams Toplantısı eklentisi kişisel kullanıcı verisi saklamaz. Toplantı bağlantıları etkinlik düzeyinde saklanır.';

// Hatalar.
$string['missingidandcmid'] = 'Ders modülü kimliği veya örnek kimliği eksik.';

// Olaylar.
$string['eventcoursemoduleviewed'] = 'Teams Toplantısı görüntülendi';
