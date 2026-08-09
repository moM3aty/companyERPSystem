SET FOREIGN_KEY_CHECKS=0;


CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `module` varchar(100) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `module`, `record_id`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'UPDATE', 'Leads', 1, 'تم تحديث حالة العميل المحتمل إلى: qualified', '::1', '2026-08-07 03:19:23'),
(2, 1, 'UPDATE', 'Leads', 1, 'تم تحديث حالة العميل المحتمل إلى: lost', '::1', '2026-08-07 03:19:24'),
(3, 1, 'LOGOUT', 'Auth', 1, 'تسجيل خروج من النظام', '::1', '2026-08-07 16:22:42'),
(4, 1, 'LOGIN', 'Auth', 1, 'تسجيل دخول للنظام (شركة: 1)', '::1', '2026-08-07 16:22:47'),
(5, 1, 'UPDATE', 'Leads', 1, 'تم تحديث حالة العميل المحتمل إلى: new', '::1', '2026-08-07 16:30:14'),
(6, 1, 'CREATE', 'Customers', 2, 'إضافة عميل جديد: Mohamed ibrahim', '::1', '2026-08-07 16:38:40'),
(7, 1, 'LOGIN', 'Auth', 1, 'تسجيل دخول للنظام (شركة: 1)', '::1', '2026-08-07 18:24:36'),
(8, 1, 'UPDATE', 'Opportunities', 1, 'تحديث مرحلة الفرصة البيعية إلى: negotiation', '::1', '2026-08-08 12:15:40'),
(9, 1, 'UPDATE', 'Opportunities', 1, 'تحديث مرحلة الفرصة البيعية إلى: proposal', '::1', '2026-08-08 12:16:32'),
(10, 1, 'UPDATE', 'Opportunities', 1, 'تحديث مرحلة الفرصة البيعية إلى: qualification', '::1', '2026-08-08 12:16:33'),
(11, 1, 'CREATE', 'Customers', 3, 'إضافة عميل جديد: العميل / المؤسسة', '::1', '2026-08-08 12:40:45'),
(12, 1, 'CREATE', 'Products', 1, 'إضافة منتج جديد: اسم المنتج', '::1', '2026-08-08 13:12:17'),
(13, 1, 'CREATE', 'Products', 2, 'إضافة منتج جديد: اسم المنتج *', '::1', '2026-08-08 13:13:22'),
(14, 1, 'UPDATE', 'Products', 2, 'تعديل بيانات المنتج: اسم المنتج *', '::1', '2026-08-08 13:49:05'),
(15, 1, 'UPDATE', 'Products', 2, 'تعديل بيانات المنتج: اسم المنتج *', '::1', '2026-08-08 13:49:29'),
(16, 1, 'CREATE', 'Invoices', 5, 'تم إصدار فاتورة مبيعات برقم INV-20260808-181 بمبلغ 400', '::1', '2026-08-08 14:18:12'),
(17, 1, 'CREATE', 'Invoices', 6, 'تم إصدار فاتورة مبيعات برقم INV-20260808-459 بمبلغ 800', '::1', '2026-08-08 14:18:43'),
(18, 1, 'CREATE', 'Invoices', 7, 'تم إصدار فاتورة مبيعات برقم INV-20260808-947 بمبلغ 2000', '::1', '2026-08-08 14:27:04'),
(19, 1, 'CREATE', 'Invoices', 8, 'تم إصدار فاتورة مبيعات برقم INV-20260808-767 بمبلغ 400', '::1', '2026-08-08 14:27:30'),
(20, 1, 'CREATE', 'Invoices', 9, 'تم إصدار فاتورة مبيعات برقم INV-20260808-506 بمبلغ 4150', '::1', '2026-08-08 14:27:47'),
(21, 1, 'CREATE', 'Campaigns', 1, 'تم إنشاء حملة تسويقية جديدة: اسم الحملة *', '::1', '2026-08-08 14:29:00'),
(22, 1, 'UPDATE', 'Campaigns', 1, 'تم تعديل بيانات الحملة التسويقية: اسم الحملة *', '::1', '2026-08-08 14:29:12'),
(23, 1, 'DELETE', 'Campaigns', 1, 'تم حذف حملة تسويقية من النظام', '::1', '2026-08-08 14:29:24'),
(24, 1, 'CREATE', 'Campaigns', 2, 'تم إنشاء حملة تسويقية جديدة: اسم الحملة *', '::1', '2026-08-08 14:29:46'),
(25, 1, 'CREATE', 'Purchases', 1, 'تم إنشاء أمر شراء جديد برقم: PO-20260808-845', '::1', '2026-08-08 14:33:28'),
(26, 1, 'UPDATE', 'Purchases', 1, 'تم استلام بضاعة لأمر الشراء PO-20260808-845 بقيمة 350', '::1', '2026-08-08 14:34:03'),
(27, 1, 'CREATE', 'Purchases', 2, 'تم إنشاء أمر شراء جديد برقم: PO-20260808-754', '::1', '2026-08-08 14:34:32'),
(28, 1, 'CREATE', 'Payment', 1, 'تسجيل سند قبض بمبلغ 400', '::1', '2026-08-08 15:23:37'),
(29, 1, 'CREATE', 'Payment', 2, 'تسجيل سند صرف بمبلغ 300', '::1', '2026-08-08 15:24:05'),
(30, 1, 'CREATE', 'Expense', 1, 'تم تسجيل مصروف بقيمة 200', '::1', '2026-08-08 15:24:43'),
(31, 1, 'CREATE', 'Expense', 2, 'تم تسجيل مصروف بقيمة 300', '::1', '2026-08-08 15:25:10'),
(32, 1, 'UPDATE', 'Stocktake', 1, 'تم اعتماد عملية الجرد وتحديث أرصدة المنتجات.', '::1', '2026-08-08 15:35:12'),
(33, 1, 'CREATE', 'Users', 3, 'إضافة مستخدم جديد للنظام: Mohamed ibrahim', '::1', '2026-08-08 17:38:46'),
(34, 1, 'COLLECT', 'Sales', 1, 'تم تحصيل مبلغ 4000 لفاتورة 9', '::1', '2026-08-08 21:27:11'),
(35, 1, 'COLLECT', 'Sales', 2, 'تم تحصيل مبلغ 150 لفاتورة 9', '::1', '2026-08-08 21:27:29'),
(36, 1, 'LOGOUT', 'Auth', 1, 'تسجيل خروج من النظام', '::1', '2026-08-08 21:53:56'),
(37, 1, 'LOGIN', 'Auth', 1, 'تسجيل دخول للنظام (شركة: 1)', '::1', '2026-08-08 21:55:51'),
(38, 1, 'CREATE', 'Invoices', 10, 'تم إصدار فاتورة مبيعات برقم INV-20260808-409 بمبلغ 800', '::1', '2026-08-08 21:56:00'),
(39, 1, 'CREATE', 'Companies', 2, 'تم تسجيل شركة جديدة بنظام SaaS: شركتي للتجارة', '::1', '2026-08-08 22:01:17'),
(40, 1, 'LOGIN', 'Auth', 1, 'تسجيل دخول للنظام (شركة: 1)', '::1', '2026-08-08 23:06:09'),
(41, 1, 'LOGOUT', 'Auth', 1, 'تسجيل خروج من النظام', '::1', '2026-08-08 23:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` enum('present','absent','late','leave') DEFAULT 'present',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `company_id`, `employee_id`, `date`, `check_in`, `check_out`, `status`, `notes`, `created_at`) VALUES
(3, 1, 2, '2026-08-08', '08:00:00', '16:00:00', 'present', '', '2026-08-08 15:59:36');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36', '2026-08-05 19:03:08');

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `type` enum('social','email','sms','print','other') DEFAULT 'social',
  `status` enum('planned','active','completed','cancelled') DEFAULT 'planned',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `budget` decimal(15,2) DEFAULT 0.00,
  `target_audience` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `campaigns`
--

INSERT INTO `campaigns` (`id`, `name`, `type`, `status`, `start_date`, `end_date`, `budget`, `target_audience`, `description`, `created_by`, `created_at`) VALUES
(2, 'اسم الحملة *', 'print', 'active', '2026-08-08', '2026-09-07', 500.00, 'الجمهور المستهدف', 'الوصف والملاحظات', 1, '2026-08-08 14:29:46');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `company_id`) VALUES
(2, 'تصنيف', '', '2026-08-08 12:37:52', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chart_of_accounts`
--

CREATE TABLE `chart_of_accounts` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('asset','liability','equity','revenue','expense') NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chart_of_accounts`
--

INSERT INTO `chart_of_accounts` (`id`, `company_id`, `code`, `name`, `type`, `parent_id`, `balance`, `is_active`, `created_at`) VALUES
(1, 1, 'رقم الحساب (الكود) *', 'اسم الحساب *', 'asset', NULL, 200.00, 1, '2026-08-08 15:19:42');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `status` enum('active','suspended') DEFAULT 'active',
  `package_id` int(11) DEFAULT 1,
  `subscription_ends_at` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `subscription_plan` varchar(50) DEFAULT 'basic',
  `subscription_end` date DEFAULT NULL,
  `max_users` int(11) DEFAULT 5,
  `max_branches` int(11) DEFAULT 1,
  `active_modules` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `domain`, `email`, `phone`, `status`, `package_id`, `subscription_ends_at`, `created_at`, `subscription_plan`, `subscription_end`, `max_users`, `max_branches`, `active_modules`) VALUES
(1, 'الشركة الرئيسية الافتراضية', NULL, NULL, NULL, 'active', 1, NULL, '2026-08-07 15:02:42', 'basic', NULL, 5, 1, NULL),
(2, 'شركتي للتجارة', 'النطاق المخصص (Subdomain) - اختياري', 'infoo@company.com', '0501234567', 'active', 1, '2026-08-31', '2026-08-08 22:01:17', 'basic', NULL, 5, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` int(11) NOT NULL,
  `contract_number` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `party_type` enum('customer','supplier','employee') NOT NULL,
  `party_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `value` decimal(15,2) DEFAULT 0.00,
  `status` enum('draft','active','expired','terminated') DEFAULT 'draft',
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT 1,
  `customer_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`id`, `contract_number`, `title`, `party_type`, `party_id`, `start_date`, `end_date`, `value`, `status`, `file_path`, `created_at`, `company_id`, `customer_name`, `description`, `created_by`) VALUES
(1, 'EMP-CON-20260807-434', 'عقد عمل محدد المدة', 'employee', 2, '2026-08-07', '2026-08-13', 15000.00, 'terminated', NULL, '2026-08-07 01:18:00', 1, '', 'نص العقد (الشروط والأحكام)', 0),
(2, 'CTR-20260808-30', 'موضوع العقد / العنوان *', 'customer', 0, '2026-08-08', '2026-08-21', 0.00, 'active', NULL, '2026-08-08 17:25:28', 1, 'العميل / المؤسسة', 'نص العقد (الشروط والأحكام)', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `type` enum('individual','company') DEFAULT 'individual',
  `balance` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `company_id`, `name`, `phone`, `email`, `address`, `company`, `type`, `balance`, `created_at`) VALUES
(2, 1, 'Mohamed ibrahim', '01124743148', 'employee@company.com', 'Plot B 16-1, 2nd industrial zone', NULL, 'individual', 0.00, '2026-08-07 16:38:40'),
(3, 1, 'العميل / المؤسسة', '01124743148', 'test@gmail.com', 'العنوان التفصيلي', NULL, 'individual', 11950.00, '2026-08-08 12:40:45');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `created_at`) VALUES
(1, 'تقنية المعلومات', '2026-08-05 17:45:22'),
(2, 'المالية', '2026-08-05 17:45:22'),
(3, 'الموارد البشرية', '2026-08-05 17:45:22'),
(4, 'التسويق', '2026-08-05 17:45:22'),
(5, 'الصيانة', '2026-08-05 17:45:22');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `folder_path` varchar(255) DEFAULT '/',
  `uploaded_by` int(11) NOT NULL,
  `access_level` enum('public','private','role_based') DEFAULT 'private',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) NOT NULL DEFAULT '/uploads/documents/'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `company_id`, `title`, `file_name`, `file_type`, `file_size`, `folder_path`, `uploaded_by`, `access_level`, `created_at`, `file_path`) VALUES
(1, 1, 'عنوان / وصف الوثيقة *', 'DOC_6a7768661ffe5_1786210406.jpg', 'jpg', 206359, '/', 1, 'private', '2026-08-08 17:33:26', '/uploads/documents/');

-- --------------------------------------------------------

--
-- Table structure for table `document_versions`
--

CREATE TABLE `document_versions` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `version_number` varchar(20) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_versions`
--

INSERT INTO `document_versions` (`id`, `document_id`, `version_number`, `file_name`, `file_path`, `file_size`, `uploaded_by`, `created_at`) VALUES
(1, 1, 'V2', '528618683_2900083920186244_956370499369716946_n.jpg', '/uploads/documents/versions/VER_6a776894b585c_1786210452.jpg', 228777, 1, '2026-08-08 17:34:12');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT 0.00,
  `hire_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `department_id` int(11) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `company_id`, `name`, `email`, `phone`, `position`, `salary`, `hire_date`, `created_at`, `department_id`, `join_date`, `status`) VALUES
(2, 1, 'Mohamed Aboelmaaty', 'mo.m3aty@gmail.com', '01275844735', 'مدير تقنية', 13000.00, '2026-08-07', '2026-08-07 01:17:47', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_advances`
--

CREATE TABLE `employee_advances` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `employee_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `deduction_month` int(11) NOT NULL,
  `deduction_year` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','deducted') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_advances`
--

INSERT INTO `employee_advances` (`id`, `company_id`, `employee_id`, `amount`, `date`, `reason`, `deduction_month`, `deduction_year`, `status`, `approved_by`, `created_at`) VALUES
(1, 1, 2, 1000.00, '2026-08-08', '', 8, 2026, 'deducted', 1, '2026-08-08 16:10:18'),
(2, 1, 2, 800.00, '2026-08-08', '', 8, 2026, 'rejected', 1, '2026-08-08 16:11:21');

-- --------------------------------------------------------

--
-- Table structure for table `employee_appraisals`
--

CREATE TABLE `employee_appraisals` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `evaluation_date` date NOT NULL,
  `performance_score` int(11) DEFAULT 0,
  `behavior_score` int(11) DEFAULT 0,
  `attendance_score` int(11) DEFAULT 0,
  `total_score` decimal(5,2) DEFAULT 0.00,
  `grade` varchar(50) DEFAULT NULL,
  `evaluator_id` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_appraisals`
--

INSERT INTO `employee_appraisals` (`id`, `employee_id`, `evaluation_date`, `performance_score`, `behavior_score`, `attendance_score`, `total_score`, `grade`, `evaluator_id`, `comments`, `created_at`) VALUES
(2, 2, '2026-08-08', 100, 100, 100, 100.00, 'ممتاز', 1, '', '2026-08-08 16:13:39');

-- --------------------------------------------------------

--
-- Table structure for table `employee_contracts`
--

CREATE TABLE `employee_contracts` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT 1,
  `employee_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `basic_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `allowances` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `category_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `expense_date` date NOT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `company_id`, `category_id`, `amount`, `expense_date`, `reference_no`, `notes`, `created_by`, `created_at`) VALUES
(2, 1, 4, 300.00, '2026-08-08', 'رقم المرجع / الفاتورة (اختياري)', 'البيان والملاحظات', 1, '2026-08-08 15:25:10');

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT 1,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `company_id`, `name`) VALUES
(1, 1, 'إيجارات'),
(2, 1, 'كهرباء ومياه'),
(3, 1, 'صيانة'),
(4, 1, 'أجور ورواتب'),
(5, 1, 'مصروفات تسويق');

-- --------------------------------------------------------

--
-- Table structure for table `financial_transactions`
--

CREATE TABLE `financial_transactions` (
  `id` int(11) NOT NULL,
  `treasury_id` int(11) NOT NULL,
  `transaction_type` enum('receipt','payment') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financial_transactions`
--

INSERT INTO `financial_transactions` (`id`, `treasury_id`, `transaction_type`, `amount`, `transaction_date`, `reference`, `description`, `created_by`, `created_at`) VALUES
(1, 1, 'receipt', 200.00, '2026-08-09', 'الرقم المرجعي (اختياري)', 'البيان (وصف الحركة) *', 1, '2026-08-08 15:22:47'),
(2, 1, 'receipt', 400.00, '2026-08-08', 'سند قبض #1', 'تحصيل مبيعات', 1, '2026-08-08 15:23:37'),
(3, 1, 'payment', 300.00, '2026-08-08', 'سند صرف #2', 'سداد مشتريات', 1, '2026-08-08 15:24:05');

-- --------------------------------------------------------

--
-- Table structure for table `fixed_assets`
--

CREATE TABLE `fixed_assets` (
  `id` int(11) NOT NULL,
  `asset_tag` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `purchase_date` date NOT NULL,
  `purchase_cost` decimal(15,2) NOT NULL,
  `salvage_value` decimal(15,2) DEFAULT 0.00,
  `useful_life_years` int(11) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `status` enum('active','maintenance','disposed','sold') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `followups`
--

CREATE TABLE `followups` (
  `id` int(11) NOT NULL,
  `lead_id` int(11) NOT NULL,
  `type` enum('call','meeting','email') DEFAULT 'call',
  `scheduled_date` datetime NOT NULL,
  `status` enum('pending','completed','canceled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT 1,
  `scheduled_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `followups`
--

INSERT INTO `followups` (`id`, `lead_id`, `type`, `scheduled_date`, `status`, `notes`, `created_by`, `created_at`, `company_id`, `scheduled_at`) VALUES
(2, 4, 'meeting', '0000-00-00 00:00:00', 'completed', 'ملاحظات أو أجندة المتابعة', 1, '2026-08-08 21:45:35', 1, '2026-08-13 00:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `follow_ups`
--

CREATE TABLE `follow_ups` (
  `id` int(11) NOT NULL,
  `opportunity_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `type` enum('call','meeting','email','task') NOT NULL,
  `subject` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `completed_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `invoice_number` varchar(50) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `sales_rep_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `company_id`, `invoice_number`, `customer_id`, `customer_name`, `total_amount`, `sales_rep_id`, `created_at`) VALUES
(5, 1, 'INV-20260808-181', NULL, 'عميل نقدي', 400.00, 1, '2026-08-08 14:18:12'),
(6, 1, 'INV-20260808-459', 3, 'العميل / المؤسسة', 800.00, 1, '2026-08-08 14:18:43'),
(7, 1, 'INV-20260808-947', 3, 'العميل / المؤسسة', 2000.00, 1, '2026-08-08 14:27:04'),
(8, 1, 'INV-20260808-767', 2, 'Mohamed ibrahim', 400.00, 1, '2026-08-08 14:27:30'),
(9, 1, 'INV-20260808-506', 3, 'العميل / المؤسسة', 4150.00, 1, '2026-08-08 14:27:47'),
(10, 1, 'INV-20260808-409', NULL, 'عميل نقدي', 800.00, 1, '2026-08-08 21:56:00');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `product_id`, `quantity`, `price`, `subtotal`, `created_at`) VALUES
(5, 5, 2, 1, 400.00, 400.00, '2026-08-08 14:18:12'),
(6, 6, 2, 2, 400.00, 800.00, '2026-08-08 14:18:43'),
(7, 7, 2, 5, 400.00, 2000.00, '2026-08-08 14:27:04'),
(8, 8, 2, 1, 400.00, 400.00, '2026-08-08 14:27:30'),
(9, 9, 2, 6, 400.00, 2400.00, '2026-08-08 14:27:47'),
(10, 9, 2, 5, 350.00, 1750.00, '2026-08-08 14:27:47'),
(11, 10, 2, 2, 400.00, 800.00, '2026-08-08 21:56:00');

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `entry_number` varchar(50) NOT NULL,
  `entry_date` date NOT NULL,
  `description` text NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journal_entries`
--

INSERT INTO `journal_entries` (`id`, `company_id`, `entry_number`, `entry_date`, `description`, `reference_type`, `reference_id`, `created_by`, `created_at`) VALUES
(1, 1, 'JE-20260808-461', '2026-08-08', 'البيان الرئيسي للقيد *', '', NULL, 1, '2026-08-08 15:27:23');

-- --------------------------------------------------------

--
-- Table structure for table `journal_lines`
--

CREATE TABLE `journal_lines` (
  `id` int(11) NOT NULL,
  `journal_entry_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `credit` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journal_lines`
--

INSERT INTO `journal_lines` (`id`, `journal_entry_id`, `account_id`, `debit`, `credit`, `description`) VALUES
(3, 1, 1, 20.00, 40.00, 'البيان (اختياري)'),
(4, 1, 1, 30.00, 10.00, 'البيان (اختياري)');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL,
  `status` enum('new','contacted','qualified','lost') DEFAULT 'new',
  `assigned_to` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `name`, `company`, `email`, `phone`, `source`, `status`, `assigned_to`, `notes`, `created_at`) VALUES
(4, 'اسم العميل', 'الشركة / المؤسسة', 'test@gmail.com', '01124743148', 'social_media', 'new', 2, 'ملاحظات إضافية', '2026-08-08 12:51:13');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `company_id`, `employee_id`, `leave_type_id`, `start_date`, `end_date`, `reason`, `status`, `approved_by`, `approved_at`, `created_at`) VALUES
(3, 1, 2, 1, '2026-09-02', '2026-09-09', 'مبررات وملاحظات الإجازة *', 'approved', 1, '2026-08-08 19:08:50', '2026-08-08 16:01:55'),
(4, 1, 2, 1, '2026-08-13', '2026-08-21', 'مبررات وملاحظات الإجازة *', 'rejected', 1, '2026-08-08 19:09:09', '2026-08-08 16:09:06');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_paid` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `name`, `is_paid`) VALUES
(1, 'إجازة سنوية', 1),
(2, 'إجازة مرضية', 1),
(3, 'إجازة بدون راتب', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opportunities`
--

CREATE TABLE `opportunities` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `stage` enum('qualification','proposal','negotiation','closed_won','closed_lost') DEFAULT 'qualification',
  `estimated_value` decimal(15,2) DEFAULT 0.00,
  `probability` int(11) DEFAULT 50,
  `expected_close_date` date DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `opportunities`
--

INSERT INTO `opportunities` (`id`, `customer_id`, `title`, `description`, `stage`, `estimated_value`, `probability`, `expected_close_date`, `assigned_to`, `created_at`) VALUES
(2, 2, 'عنوان الفرصة', 'الوصف والملاحظات', 'proposal', 5000.00, 56, '2026-08-15', 2, '2026-08-08 12:50:11');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `reference_type` enum('invoice','purchase_order') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `reference_id`, `reference_type`, `amount`, `payment_method`, `notes`, `created_at`) VALUES
(1, 8, 'invoice', 400.00, 'cash', 'ملاحظات / رقم الحوالة', '2026-08-08 15:23:37'),
(2, 1, 'purchase_order', 300.00, 'bank_transfer', 'ملاحظات / رقم الحوالة', '2026-08-08 15:24:05');

-- --------------------------------------------------------

--
-- Table structure for table `payrolls`
--

CREATE TABLE `payrolls` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `reference_no` varchar(50) NOT NULL,
  `month` tinyint(2) NOT NULL,
  `year` year(4) NOT NULL,
  `total_employees` int(11) NOT NULL DEFAULT 0,
  `total_net_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','approved','paid') DEFAULT 'approved',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payrolls`
--

INSERT INTO `payrolls` (`id`, `company_id`, `reference_no`, `month`, `year`, `total_employees`, `total_net_amount`, `status`, `created_by`, `created_at`) VALUES
(1, 1, 'PAY-202608-98', 8, '2026', 1, 14000.00, 'approved', 1, '2026-08-08 16:14:51');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_details`
--

CREATE TABLE `payroll_details` (
  `id` int(11) NOT NULL,
  `payroll_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `employee_name` varchar(100) NOT NULL,
  `base_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bonuses` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_details`
--

INSERT INTO `payroll_details` (`id`, `payroll_id`, `employee_id`, `employee_name`, `base_salary`, `deductions`, `bonuses`, `net_salary`) VALUES
(1, 1, 2, 'Mohamed Aboelmaaty', 13000.00, 1200.00, 2200.00, 14000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `sku` varchar(50) NOT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `unit` varchar(50) DEFAULT 'قطعة',
  `quantity` int(11) DEFAULT 0,
  `reorder_point` int(11) DEFAULT 5,
  `track_batches` tinyint(1) DEFAULT 0,
  `price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cost` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `company_id`, `category_id`, `name`, `description`, `sku`, `barcode`, `unit`, `quantity`, `reorder_point`, `track_batches`, `price`, `created_at`, `cost`) VALUES
(2, 1, 2, 'اسم المنتج *', 'وصف المنتج (ملاحظات)', 'رمز التخزين (SKU)', 'الباركود الدولي (Barcode)', 'قطعة', 199, 5, 1, 400.00, '2026-08-08 13:13:22', 350.00);

-- --------------------------------------------------------

--
-- Table structure for table `product_batches`
--

CREATE TABLE `product_batches` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_number` varchar(100) NOT NULL COMMENT 'رقم التشغيلة / Lot',
  `production_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT 1,
  `serial_number` varchar(100) DEFAULT NULL,
  `lot_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_batches`
--

INSERT INTO `product_batches` (`id`, `product_id`, `batch_number`, `production_date`, `expiry_date`, `quantity`, `created_at`, `company_id`, `serial_number`, `lot_number`, `notes`, `status`) VALUES
(1, 2, 'رقم التشغيلة (Lot Number)', '2026-08-15', '2026-08-20', 1, '2026-08-08 15:04:40', 1, 'رقم السيريال (القطعة) - إن وجد', 'رقم التشغيلة (Lot Number)', 'ملاحظات إضافية', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('planning','active','on_hold','completed','cancelled') DEFAULT 'planning',
  `budget` decimal(15,2) DEFAULT 0.00,
  `project_manager` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `company_id`, `name`, `code`, `customer_id`, `description`, `start_date`, `end_date`, `status`, `budget`, `project_manager`, `created_at`, `created_by`) VALUES
(2, 1, 'اسم المشروع *', 'كود المشروع (Code) *', 3, 'وصف ونطاق المشروع', '2026-08-08', '2026-08-29', 'on_hold', 55000.00, NULL, '2026-08-08 16:17:24', 0),
(3, 1, 'Mohamed Aboelmaaty', 'كود المستودع  *', NULL, 'وصف المشروع / نطاق العمل', '2026-08-08', '2026-08-15', 'active', 80000.00, NULL, '2026-08-08 16:33:27', 1);

-- --------------------------------------------------------

--
-- Table structure for table `project_tasks`
--

CREATE TABLE `project_tasks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `start_date` date NOT NULL,
  `due_date` date NOT NULL,
  `progress` int(11) DEFAULT 0,
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_tasks`
--

INSERT INTO `project_tasks` (`id`, `project_id`, `title`, `start_date`, `due_date`, `progress`, `assigned_to`, `created_at`) VALUES
(1, 2, 'إضافة مهمة', '2026-08-08', '2026-08-14', 35, 2, '2026-08-08 16:17:58'),
(2, 2, 'عنوان المهمة *', '2026-08-08', '2026-08-25', 65, 2, '2026-08-08 16:35:25');

-- --------------------------------------------------------

--
-- Table structure for table `project_timesheets`
--

CREATE TABLE `project_timesheets` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `total_hours` decimal(5,2) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `company_id` int(11) DEFAULT 1,
  `status` varchar(50) DEFAULT 'approved',
  `supplier_name` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `po_number` varchar(50) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('pending','approved','ordered','delivered','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `company_id`, `po_number`, `supplier_id`, `total_amount`, `status`, `notes`, `received_date`, `created_at`) VALUES
(1, 1, 'PO-20260808-845', 2, 350.00, 'delivered', 'السبب / ملاحظات الطلب *', '2026-08-08', '2026-08-08 14:33:28');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_ordered` int(11) NOT NULL,
  `quantity_received` int(11) DEFAULT 0,
  `unit_price` decimal(10,2) NOT NULL,
  `total` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `po_id`, `product_id`, `quantity_ordered`, `quantity_received`, `unit_price`, `total`) VALUES
(1, 1, 2, 1, 1, 350.00, 350.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requests`
--

CREATE TABLE `purchase_requests` (
  `id` int(11) NOT NULL,
  `request_number` varchar(50) NOT NULL,
  `requested_by` int(11) NOT NULL,
  `request_date` date NOT NULL,
  `status` enum('pending','approved','rejected','ordered') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_requests`
--

INSERT INTO `purchase_requests` (`id`, `request_number`, `requested_by`, `request_date`, `status`, `notes`, `approved_by`, `approved_at`, `created_at`) VALUES
(1, 'PRQ-20260808-290', 1, '2026-08-09', 'rejected', 'السبب / ملاحظات الطلب *', 1, '2026-08-08 17:30:40', '2026-08-08 14:30:21'),
(2, 'PRQ-20260808-881', 1, '2026-08-10', 'approved', 'السبب / ملاحظات الطلب *', 1, '2026-08-08 17:31:01', '2026-08-08 14:30:58');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_items`
--

CREATE TABLE `purchase_request_items` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `estimated_price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_request_items`
--

INSERT INTO `purchase_request_items` (`id`, `request_id`, `product_id`, `quantity`, `estimated_price`) VALUES
(1, 1, 2, 1, 350.00),
(2, 2, 2, 1, 400.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` int(11) NOT NULL,
  `return_number` varchar(50) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) NOT NULL,
  `total_refund` decimal(15,2) DEFAULT 0.00,
  `reason` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT 1,
  `supplier_name` varchar(255) DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'approved',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_returns`
--

INSERT INTO `purchase_returns` (`id`, `return_number`, `po_id`, `supplier_id`, `total_refund`, `reason`, `created_by`, `created_at`, `company_id`, `supplier_name`, `return_date`, `total_amount`, `status`, `notes`) VALUES
(5, 'PRT-26080811', NULL, 2, 0.00, NULL, 1, '2026-08-08 21:21:06', 1, 'اسم المورد أو الشركة *', '2026-08-08', 350.00, 'draft', 'سبب الارتجاع / ملاحظات');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_items`
--

CREATE TABLE `purchase_return_items` (
  `id` int(11) NOT NULL,
  `return_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_return_items`
--

INSERT INTO `purchase_return_items` (`id`, `return_id`, `product_id`, `quantity`, `price`, `subtotal`, `unit_cost`) VALUES
(6, 5, 2, 1, 0.00, 350.00, 350.00);

-- --------------------------------------------------------

--
-- Table structure for table `quotes`
--

CREATE TABLE `quotes` (
  `id` int(11) NOT NULL,
  `quote_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','sent','accepted','rejected') DEFAULT 'draft',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotes`
--

INSERT INTO `quotes` (`id`, `quote_number`, `customer_id`, `total_amount`, `status`, `created_by`, `created_at`) VALUES
(1, 'QTE-202608-960', 3, 2100.00, 'accepted', 1, '2026-08-08 13:14:08');

-- --------------------------------------------------------

--
-- Table structure for table `quote_items`
--

CREATE TABLE `quote_items` (
  `id` int(11) NOT NULL,
  `quote_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quote_items`
--

INSERT INTO `quote_items` (`id`, `quote_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 1, 2, 6, 350.00, 2100.00);

-- --------------------------------------------------------

--
-- Table structure for table `saas_packages`
--

CREATE TABLE `saas_packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `max_users` int(11) NOT NULL DEFAULT 5,
  `max_storage_mb` int(11) NOT NULL DEFAULT 1024,
  `price_monthly` decimal(10,2) NOT NULL DEFAULT 0.00,
  `features_desc` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saas_packages`
--

INSERT INTO `saas_packages` (`id`, `name`, `max_users`, `max_storage_mb`, `price_monthly`, `features_desc`) VALUES
(1, 'الباقة الأساسية', 5, 2048, 99.00, 'إدارة المبيعات والمخزون الأساسية'),
(2, 'الباقة الاحترافية', 20, 10240, 299.00, 'جميع الميزات + شؤون الموظفين والمشاريع'),
(3, 'باقة الشركات (غير محدودة)', 9999, 51200, 599.00, 'وصول كامل بدون قيود مع دعم فني 24/7');

-- --------------------------------------------------------

--
-- Table structure for table `sales_collections`
--

CREATE TABLE `sales_collections` (
  `id` int(11) NOT NULL,
  `receipt_number` varchar(50) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `treasury_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `collection_date` date NOT NULL,
  `payment_method` enum('cash','bank_transfer','check') DEFAULT 'cash',
  `reference` varchar(100) DEFAULT NULL COMMENT 'رقم الشيك أو الحوالة',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_collections`
--

INSERT INTO `sales_collections` (`id`, `receipt_number`, `invoice_id`, `treasury_id`, `amount`, `collection_date`, `payment_method`, `reference`, `notes`, `created_by`, `created_at`, `company_id`) VALUES
(1, 'REC-1786224431', 9, 1, 4000.00, '2026-08-08', 'cash', 'الرقم المرجعي (اختياري)', 'ملاحظات التحصيل', 1, '2026-08-08 21:27:11', 1),
(2, 'REC-1786224449', 9, 1, 150.00, '2026-08-08', 'bank_transfer', 'الرقم المرجعي (اختياري)', 'ملاحظات التحصيل', 1, '2026-08-08 21:27:29', 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `status` varchar(50) DEFAULT 'draft',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT 1,
  `customer_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_orders`
--

INSERT INTO `sales_orders` (`id`, `order_number`, `customer_id`, `order_date`, `status`, `total_amount`, `notes`, `created_by`, `created_at`, `company_id`, `customer_name`) VALUES
(4, 'SO-26080812', 3, '2026-08-08', 'sent', 400.00, 'ملاحظات / شروط خاصة', 1, '2026-08-08 21:07:45', 1, 'العميل / المؤسسة'),
(5, 'SO-26080824', 3, '2026-08-08', 'approved', 400.00, '', 1, '2026-08-08 21:11:05', 1, 'العميل / المؤسسة');

-- --------------------------------------------------------

--
-- Table structure for table `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_order_items`
--

INSERT INTO `sales_order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`, `unit_price`) VALUES
(12, 5, 2, 1, 0.00, 400.00, 400.00),
(13, 4, 2, 1, 0.00, 400.00, 400.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales_returns`
--

CREATE TABLE `sales_returns` (
  `id` int(11) NOT NULL,
  `return_number` varchar(50) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `total_refund` decimal(15,2) DEFAULT 0.00,
  `reason` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_returns`
--

INSERT INTO `sales_returns` (`id`, `return_number`, `invoice_id`, `total_refund`, `reason`, `created_by`, `created_at`) VALUES
(1, 'RET-20260808-543', 8, 400.00, 'انا حر', 1, '2026-08-08 18:00:09');

-- --------------------------------------------------------

--
-- Table structure for table `sales_return_items`
--

CREATE TABLE `sales_return_items` (
  `id` int(11) NOT NULL,
  `return_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_return_items`
--

INSERT INTO `sales_return_items` (`id`, `return_id`, `product_id`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 2, 1, 400.00, 400.00);

-- --------------------------------------------------------

--
-- Table structure for table `sanctions`
--

CREATE TABLE `sanctions` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `type` enum('warning','deduction') NOT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `date` date NOT NULL,
  `reason` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sanctions`
--

INSERT INTO `sanctions` (`id`, `employee_id`, `type`, `amount`, `date`, `reason`, `created_by`, `created_at`) VALUES
(2, 2, 'deduction', 200.00, '2026-08-08', 'سبب المخالفة والمبررات *', 1, '2026-08-08 16:12:27'),
(3, 2, 'warning', 0.00, '2026-08-08', 'سبب المخالفة والمبررات *', 1, '2026-08-08 16:12:47');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`, `updated_at`, `company_id`) VALUES
('app_version', '2.0.0', '2026-08-05 17:45:22', 1),
('company_email', 'info@company.com', '2026-08-08 22:12:53', 1),
('company_logo', 'uploads/logo_1_1786227146.png', '2026-08-08 22:12:26', 1),
('company_name', 'Nour Trust', '2026-08-08 22:12:53', 1),
('company_phone', '0501234567', '2026-08-08 22:12:53', 1),
('currency', 'ر.س', '2026-08-08 22:12:53', 1),
('tax_rate', '15', '2026-08-08 22:12:53', 1),
('vat_number', '', '2026-08-08 22:12:53', 1);

-- --------------------------------------------------------

--
-- Table structure for table `stocktakes`
--

CREATE TABLE `stocktakes` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT 1,
  `reference` varchar(50) NOT NULL,
  `stocktake_date` date NOT NULL,
  `status` varchar(50) DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stocktakes`
--

INSERT INTO `stocktakes` (`id`, `company_id`, `reference`, `stocktake_date`, `status`, `notes`, `created_by`, `created_at`) VALUES
(1, 1, 'STK-20260808-64', '2026-08-08', 'in_progress', '', 1, '2026-08-08 18:29:46'),
(2, 1, 'STK-20260808-91', '2026-08-08', 'draft', '', 1, '2026-08-08 18:35:48');

-- --------------------------------------------------------

--
-- Table structure for table `stocktake_items`
--

CREATE TABLE `stocktake_items` (
  `id` int(11) NOT NULL,
  `stocktake_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `system_quantity` int(11) NOT NULL DEFAULT 0,
  `actual_quantity` int(11) NOT NULL DEFAULT 0,
  `variance` int(11) NOT NULL DEFAULT 0,
  `notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stocktake_items`
--

INSERT INTO `stocktake_items` (`id`, `stocktake_id`, `product_id`, `system_quantity`, `actual_quantity`, `variance`, `notes`) VALUES
(1, 1, 2, 482, 200, -282, '');

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments`
--

CREATE TABLE `stock_adjustments` (
  `id` int(11) NOT NULL,
  `reference_no` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `type` enum('addition','subtraction','damage','loss') NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_adjustments`
--

INSERT INTO `stock_adjustments` (`id`, `reference_no`, `date`, `type`, `product_id`, `quantity`, `notes`, `created_by`, `created_at`) VALUES
(1, 'ADJ-20260808-872', '2026-08-08', 'addition', 2, 1, 'السبب / الملاحظات', 1, '2026-08-08 15:07:06');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` int(11) NOT NULL,
  `transfer_number` varchar(20) NOT NULL,
  `from_warehouse_id` int(11) NOT NULL,
  `to_warehouse_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('pending','approved','completed','cancelled') DEFAULT 'pending',
  `requested_by` int(11) NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `type` enum('company','individual') DEFAULT 'company',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tax_number` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `company_id`, `name`, `contact_person`, `phone`, `email`, `address`, `company`, `balance`, `type`, `notes`, `created_at`, `tax_number`) VALUES
(2, 1, 'اسم المورد أو الشركة *', 'الشخص المسؤول (Contact Person)', '01275844735', 'test@gmail.com', 'العنوان الوطني / موقع المستودع', NULL, 50.00, 'company', 'ملاحظات وشروط التعامل', '2026-08-08 14:32:59', NULL),
(3, 1, 'اسم المورد أو الشركة *', NULL, '01275844735', 'test@gmail.com', 'العنوان الوطني / ملاحظات', NULL, 20.00, 'company', NULL, '2026-08-08 22:33:24', '4684165316519654165');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT 1,
  `ticket_number` varchar(50) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') DEFAULT 'open',
  `assigned_to` int(11) DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `company_id`, `ticket_number`, `customer_id`, `subject`, `description`, `priority`, `status`, `assigned_to`, `resolved_at`, `created_at`) VALUES
(1, 1, 'TKT-20260808-484', 3, 'عنوان التذكرة (Subject) *', 'وصف المشكلة بالتفصيل *', 'medium', 'resolved', 2, '2026-08-08 20:37:48', '2026-08-08 17:37:30');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_comments`
--

CREATE TABLE `ticket_comments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `timesheets`
--

CREATE TABLE `timesheets` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT 1,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `employee_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `total_hours` decimal(5,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `timesheets`
--

INSERT INTO `timesheets` (`id`, `company_id`, `project_id`, `task_id`, `employee_id`, `date`, `start_time`, `end_time`, `total_hours`, `note`, `created_at`) VALUES
(1, 1, 2, 1, 2, '2026-08-08', '19:27:00', '19:27:00', 0.00, 'ملاحظات العمل', '2026-08-08 19:27:13');

-- --------------------------------------------------------

--
-- Table structure for table `treasuries`
--

CREATE TABLE `treasuries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('cash','bank') DEFAULT 'cash',
  `account_number` varchar(50) DEFAULT NULL,
  `current_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT 1,
  `balance` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `treasuries`
--

INSERT INTO `treasuries` (`id`, `name`, `type`, `account_number`, `current_balance`, `created_at`, `company_id`, `balance`) VALUES
(1, 'الصندوق الرئيسي', 'cash', NULL, 300.00, '2026-08-07 01:16:51', 1, 4150.00);

-- --------------------------------------------------------

--
-- Table structure for table `treasury_transactions`
--

CREATE TABLE `treasury_transactions` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT 1,
  `treasury_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'deposit',
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `transaction_date` date NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `treasury_transactions`
--

INSERT INTO `treasury_transactions` (`id`, `company_id`, `treasury_id`, `type`, `amount`, `transaction_date`, `reference`, `notes`, `created_by`, `created_at`) VALUES
(1, 1, 1, 'deposit', 2000.00, '2026-08-08', '', '', 1, '2026-08-09 00:31:45'),
(2, 1, 1, 'withdrawal', 2000.00, '2026-08-08', '', '', 1, '2026-08-09 00:32:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','manager','accountant','sales_rep','hr','editor','viewer') DEFAULT 'viewer',
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `company_id`, `name`, `email`, `password`, `role`, `phone`, `created_at`) VALUES
(1, 1, 'المدير العام', 'admin@system.com', '$2y$10$1199GGEUpKz6rKoUUO6x0eWNdIinAoPENj0p.873bOhbQQrRg3jOW', 'super_admin', '', '2026-08-07 01:16:48'),
(2, 1, 'Mohamed Aboelmaaty', 'mo.m3aty@gmail.com', '$2y$10$H653XXovSDonP2YXTxMJAutQrEjJmSRc1yDD9ckel0FFxtTzXGGFa', '', '01124743148', '2026-08-07 01:42:02'),
(3, 1, 'Mohamed ibrahim', 'employee@company.com', '$2y$10$upb1rEVid5cyipguF5V0ZuFp80LBxVAmzyQtx/NQsFn4vlDuO.JMK', 'editor', NULL, '2026-08-08 17:38:46'),
(5, 2, 'اسم المدير *', 'infooo@company.com', '$2y$10$N6TXd80RsSnsTbs/fIDUYeGoCQlImxj2.HR9zxEi7dSckiE3Q4S7K', 'admin', NULL, '2026-08-08 22:01:17');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT 1,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `company_id`, `name`, `code`, `address`, `is_main`, `created_at`) VALUES
(1, 1, 'المستودع الرئيسي', 'WH-001', NULL, 0, '2026-08-05 17:45:22'),
(2, 1, 'مستودع فرع الرياض', 'WH-002', '', 1, '2026-08-05 17:45:22'),
(3, 1, 'اسم المستودع أو الفرع *', 'كود المستودع (معرف ف', 'العنوان والموقع الجغرافي', 0, '2026-08-08 14:35:42'),
(6, 1, 'اسم المستودع أو الفرع', 'كود المستودع  *', 'العنوان والموقع الجغرافي', 1, '2026-08-08 14:37:08');

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_stock`
--

CREATE TABLE `warehouse_stock` (
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouse_stock`
--

INSERT INTO `warehouse_stock` (`product_id`, `warehouse_id`, `quantity`, `updated_at`) VALUES
(2, 1, 5, '2026-08-05 17:45:22');


--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_user` (`user_id`);

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_coa_company` (`company_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_company_package` (`package_id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contract_number` (`contract_number`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cust_company` (`company_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_emp_company` (`company_id`);

--
-- Indexes for table `employee_advances`
--
ALTER TABLE `employee_advances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `employee_appraisals`
--
ALTER TABLE `employee_appraisals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `employee_contracts`
--
ALTER TABLE `employee_contracts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fk_exp_company` (`company_id`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_expcat_company` (`company_id`);

--
-- Indexes for table `financial_transactions`
--
ALTER TABLE `financial_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `treasury_id` (`treasury_id`);

--
-- Indexes for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_tag` (`asset_tag`);

--
-- Indexes for table `followups`
--
ALTER TABLE `followups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_id` (`lead_id`);

--
-- Indexes for table `follow_ups`
--
ALTER TABLE `follow_ups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_followup_opportunity` (`opportunity_id`),
  ADD KEY `fk_followup_customer` (`customer_id`),
  ADD KEY `fk_followup_created_by` (`created_by`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `fk_inv_company` (`company_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `entry_number` (`entry_number`),
  ADD KEY `fk_je_company` (`company_id`);

--
-- Indexes for table `journal_lines`
--
ALTER TABLE `journal_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entry_id` (`journal_entry_id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `leave_type_id` (`leave_type_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notification_user` (`user_id`);

--
-- Indexes for table `opportunities`
--
ALTER TABLE `opportunities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payrolls`
--
ALTER TABLE `payrolls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_no` (`reference_no`);

--
-- Indexes for table `payroll_details`
--
ALTER TABLE `payroll_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_id` (`payroll_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fk_prod_company` (`company_id`);

--
-- Indexes for table `product_batches`
--
ALTER TABLE `product_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_batch_product` (`product_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_proj_company` (`company_id`);

--
-- Indexes for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_timesheets`
--
ALTER TABLE `project_timesheets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `po_number` (`po_number`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `fk_po_company` (`company_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `request_number` (`request_number`);

--
-- Indexes for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `return_number` (`return_number`);

--
-- Indexes for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `return_id` (`return_id`);

--
-- Indexes for table `quotes`
--
ALTER TABLE `quotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quote_number` (`quote_number`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `quote_items`
--
ALTER TABLE `quote_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quote_id` (`quote_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `saas_packages`
--
ALTER TABLE `saas_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_collections`
--
ALTER TABLE `sales_collections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `receipt_number` (`receipt_number`),
  ADD KEY `fk_collection_invoice` (`invoice_id`),
  ADD KEY `fk_collection_treasury` (`treasury_id`);

--
-- Indexes for table `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

--
-- Indexes for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `sales_returns`
--
ALTER TABLE `sales_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `return_number` (`return_number`);

--
-- Indexes for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `return_id` (`return_id`);

--
-- Indexes for table `sanctions`
--
ALTER TABLE `sanctions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`company_id`,`setting_key`);

--
-- Indexes for table `stocktakes`
--
ALTER TABLE `stocktakes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stocktake_items`
--
ALTER TABLE `stocktake_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_no` (`reference_no`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transfer_number` (`transfer_number`),
  ADD KEY `fk_transfer_from_wh` (`from_warehouse_id`),
  ADD KEY `fk_transfer_to_wh` (`to_warehouse_id`),
  ADD KEY `fk_transfer_product` (`product_id`),
  ADD KEY `fk_transfer_requested_by` (`requested_by`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_supp_company` (`company_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`);

--
-- Indexes for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `timesheets`
--
ALTER TABLE `timesheets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `treasuries`
--
ALTER TABLE `treasuries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `treasury_transactions`
--
ALTER TABLE `treasury_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_company` (`company_id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `warehouse_stock`
--
ALTER TABLE `warehouse_stock`
  ADD PRIMARY KEY (`product_id`,`warehouse_id`),
  ADD KEY `fk_ws_warehouse` (`warehouse_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `document_versions`
--
ALTER TABLE `document_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_advances`
--
ALTER TABLE `employee_advances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_appraisals`
--
ALTER TABLE `employee_appraisals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employee_contracts`
--
ALTER TABLE `employee_contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `financial_transactions`
--
ALTER TABLE `financial_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followups`
--
ALTER TABLE `followups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `follow_ups`
--
ALTER TABLE `follow_ups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `journal_lines`
--
ALTER TABLE `journal_lines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opportunities`
--
ALTER TABLE `opportunities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payrolls`
--
ALTER TABLE `payrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payroll_details`
--
ALTER TABLE `payroll_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_batches`
--
ALTER TABLE `product_batches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project_tasks`
--
ALTER TABLE `project_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_timesheets`
--
ALTER TABLE `project_timesheets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_requests`
--
ALTER TABLE `purchase_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quotes`
--
ALTER TABLE `quotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quote_items`
--
ALTER TABLE `quote_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `saas_packages`
--
ALTER TABLE `saas_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sales_collections`
--
ALTER TABLE `sales_collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `sales_returns`
--
ALTER TABLE `sales_returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sanctions`
--
ALTER TABLE `sanctions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stocktakes`
--
ALTER TABLE `stocktakes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stocktake_items`
--
ALTER TABLE `stocktake_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timesheets`
--
ALTER TABLE `timesheets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `treasuries`
--
ALTER TABLE `treasuries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `treasury_transactions`
--
ALTER TABLE `treasury_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD CONSTRAINT `campaigns_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD CONSTRAINT `fk_coa_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `fk_company_package` FOREIGN KEY (`package_id`) REFERENCES `saas_packages` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `fk_cust_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD CONSTRAINT `document_versions_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_versions_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_emp_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_advances`
--
ALTER TABLE `employee_advances`
  ADD CONSTRAINT `employee_advances_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_appraisals`
--
ALTER TABLE `employee_appraisals`
  ADD CONSTRAINT `employee_appraisals_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`),
  ADD CONSTRAINT `fk_exp_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD CONSTRAINT `fk_expcat_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `financial_transactions`
--
ALTER TABLE `financial_transactions`
  ADD CONSTRAINT `financial_transactions_ibfk_1` FOREIGN KEY (`treasury_id`) REFERENCES `treasuries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `followups`
--
ALTER TABLE `followups`
  ADD CONSTRAINT `followups_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `follow_ups`
--
ALTER TABLE `follow_ups`
  ADD CONSTRAINT `fk_followup_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_followup_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_followup_opportunity` FOREIGN KEY (`opportunity_id`) REFERENCES `opportunities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_inv_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `fk_je_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_lines`
--
ALTER TABLE `journal_lines`
  ADD CONSTRAINT `journal_lines_ibfk_1` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_requests_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notification_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `opportunities`
--
ALTER TABLE `opportunities`
  ADD CONSTRAINT `opportunities_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_details`
--
ALTER TABLE `payroll_details`
  ADD CONSTRAINT `payroll_details_ibfk_1` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_prod_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_batches`
--
ALTER TABLE `product_batches`
  ADD CONSTRAINT `fk_batch_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_proj_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD CONSTRAINT `project_tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_timesheets`
--
ALTER TABLE `project_timesheets`
  ADD CONSTRAINT `project_timesheets_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_timesheets_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `project_tasks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `project_timesheets_ibfk_3` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `fk_po_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `purchase_request_items`
--
ALTER TABLE `purchase_request_items`
  ADD CONSTRAINT `purchase_request_items_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `purchase_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD CONSTRAINT `purchase_return_items_ibfk_1` FOREIGN KEY (`return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotes`
--
ALTER TABLE `quotes`
  ADD CONSTRAINT `quotes_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `quote_items`
--
ALTER TABLE `quote_items`
  ADD CONSTRAINT `quote_items_ibfk_1` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quote_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `sales_collections`
--
ALTER TABLE `sales_collections`
  ADD CONSTRAINT `fk_collection_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_collection_treasury` FOREIGN KEY (`treasury_id`) REFERENCES `treasuries` (`id`);

--
-- Constraints for table `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD CONSTRAINT `sales_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_return_items`
--
ALTER TABLE `sales_return_items`
  ADD CONSTRAINT `sales_return_items_ibfk_1` FOREIGN KEY (`return_id`) REFERENCES `sales_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sanctions`
--
ALTER TABLE `sanctions`
  ADD CONSTRAINT `sanctions_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD CONSTRAINT `fk_transfer_from_wh` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`),
  ADD CONSTRAINT `fk_transfer_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_transfer_requested_by` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_transfer_to_wh` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `fk_supp_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket_comments`
--
ALTER TABLE `ticket_comments`
  ADD CONSTRAINT `ticket_comments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouse_stock`
--
ALTER TABLE `warehouse_stock`
  ADD CONSTRAINT `fk_ws_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ws_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
SET FOREIGN_KEY_CHECKS=1;