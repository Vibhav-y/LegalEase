<?php
// Logo component that can be included in any page
$logoText = "LegalEase";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="flex items-center">
    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center mr-3 transform hover:scale-110 transition-transform duration-200">
        <i class="fas fa-balance-scale text-white text-lg"></i>
    </div>
    <span class="text-primary text-2xl font-semibold tracking-normal dark:text-blue-400">
        <?php echo $logoText; ?>
    </span>
</div>

<style>
.dark .text-primary {
    color: #60a5fa !important;
}
</style> 