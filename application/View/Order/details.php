<section class="flexbox flexbox-center">
    <div class="w12">
        <a href="<?= $this->getActionUrl('index'); ?>" class="breadcrumb flexbox">
            <span class="material-icons">arrow_back</span><span>Zurück</span>
        </a>
    </div>
    <div class="w12 m05 flexbox flex-start flex-col">
        <div class="flexbox w12">
            <h2 class="h2">Orderdetails</h2>
            <a href="<?= $this->getActionUrl('edit'); ?>?id=<?= $this->order_id; ?>">
                <button class="btn flexbox"><span class="material-icons">edit</span>&nbsp; Order bearbeiten
                </button>
            </a>
        </div>
    </div>
</section>
