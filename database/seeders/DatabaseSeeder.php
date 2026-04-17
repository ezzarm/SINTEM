<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan penting! Ikuti urutan FK dependency
        $this->call([
            RolesSeeder::class,           // 1. roles (tidak ada FK)
            UsersSeeder::class,           // 2. users (FK -> roles)
            ReportCategoriesSeeder::class,// 3. report_categories (FK -> roles)
            EventCategoriesSeeder::class, // 4. event_categories (tidak ada FK)
            EventLocationsSeeder::class,  // 5. event_locations (tidak ada FK)
            AnnouncementsSeeder::class,   // 6. announcements (FK -> users)
            EventsSeeder::class,          // 7. events (FK -> event_categories, event_locations, users)
            AnonymousReportsSeeder::class,// 8. anonymous_reports (FK -> report_categories)
            AttachmentsSeeder::class,     // 9. attachments (FK -> users)
            LostFoundsSeeder::class,      // 10. lost_founds
            PhotosSeeder::class,          // 11. photos (FK -> users)
        ]);
    }
}
