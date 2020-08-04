<div class="search-sidebar panel panel-default">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-search"></span> Поиск
    </div>
    <div class="panel-body">
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <label for="q-input"></label>
                <input id="q-input" type="text" name="q" class="form-control"
                       placeholder="<?= Yii::t('app', 'Search') . '...' ?>">

                <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                <i class="fa fa-search"></i>
            </button>
        </span>
            </div>
        </form>
    </div>
</div>
