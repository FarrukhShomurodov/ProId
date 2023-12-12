<script >

</script>

<template>
    <div class="main_container">
        <div class="main_content_parts">
            <h1>Данные</h1>
            <div class="content_container_data">
                <div class="main_content_container" @click="$router.push({ name: 'dashboard-data' })">Обо мне</div>
                <div class="main_content_container" @click="$router.push({ name: 'dashboard-data' })">Адреса</div>
            </div>
        </div>
        <div class="main_content_parts">
            <h1>PRO PAY</h1>
            <div class="content_container_data">
                <div class="main_content_container" @click="$router.push({ name: 'dashboard-pay' })">Карты</div>
                <div class="main_content_container" @click="$router.push({ name: 'dashboard-pay' })">История платежей</div>
                <div class="main_content_container" @click="$router.push({ name: 'dashboard-pay' })">Кеш бек</div>
            </div>
        </div>
        <div class="main_content_parts">
            <h1>Безопастность</h1>
            <div class="content_container_data security">
                <div class="main_content_container">Активность в аккаунте</div>
                <div class="main_content_container">Доступы</div>
                <div class="main_content_container">Способы входа</div>
                <div class="main_content_container">Средства восста-новления</div>
            </div>
        </div>
        <div class="main_content_parts">
            <h1>PRO BUSINESS</h1>
            <div class="content_container_data">
                <div class="main_content_container" @click="$router.push({ name: 'dashboard-business' })">Мои бизнесы</div>
            </div>
        </div>
        <div class="main_content_parts">
            <h1>Подписки</h1>
            <div class="content_container_data">
                <div class="main_content_container">Скоро</div>
            </div>
        </div>
    </div>
</template>

<style >
@import "/public/style/dashboard/main.css";
</style>
