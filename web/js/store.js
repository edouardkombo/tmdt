
function AdminController($scope, $filter) {
    $scope.isActive = true;
    $scope.sections = [
//        { name: 'Grid View', class: "cbp-vm-grid" },
        { name: 'List View', class: "cbp-vm-list"}];

        $scope.setMaster = function (section) {
            $scope.selected = section;
            $scope.isActive = !$scope.isActive;
        };

    $scope.isSelected = function (section) {
        return $scope.selected === section;
    };

    var myStore = new store();
    $scope.currentPage = 0;
    $scope.pageSize = 9;
    $scope.numberOfPages = Math.ceil(myStore.products.length / $scope.pageSize);

    $scope.filteredItems = [];
    $scope.groupedItems = [];
    $scope.pagedItems = [];

    var searchMatch = function (haystack, needle) {
        if (!needle) {
            return true;
        }
        return haystack.toLowerCase().indexOf(needle.toLowerCase()) !== -1;
    };
    $scope.search = function (name) {
        $scope.filteredItems = $filter('filter')(myStore.products, function (product) {
            for (var attr in product) {
                if (searchMatch(product[name], $scope.query))
                    return true;
            }
            return false;
        });
        $scope.currentPage = 0;
        $scope.groupToPages();
    };
    $scope.myFilter = function (column,category) {
        $scope.filteredItems = $filter('filter')(myStore.products, function (product) {
            for (var attr in product) {
                if (searchMatch(product[column], category))
                    return true;
            }
            return false;
        });
        $scope.currentPage = 0;
        $scope.groupToPages();
    };
    $scope.groupToPages = function () {
        $scope.pagedItems = [];

        for (var i = 0; i < $scope.filteredItems.length; i++) {
            if (i % $scope.pageSize === 0) {
                $scope.pagedItems[Math.floor(i / $scope.pageSize)] = [$scope.filteredItems[i]];
            } else {
                $scope.pagedItems[Math.floor(i / $scope.pageSize)].push($scope.filteredItems[i]);
            }
        }
    };
    // functions have been describe process the data for display
    $scope.myFilter();
    $scope.search(); 
}
AdminController.$inject = ['$scope', '$filter'];

function store() {
    this.products = [
        { num: 1, code: 'TMDT-message', category: 'mac', name: 'Share a 60 seconds ephemeral message with someone and the world.', src: "social-talk1.jpg", description: 'Announce a special message for someone like, a wedding, your love, a dinner invitation, a special gift for a birthday (in image), a travel invitation, a job offer... anything positive, be imaginative. Just write your message and tell the desired people to connect to this website, they, with the whole world will see your message at the same time.', price: 1.99, cal: 10 }];
     
}

function detailsprod() {
    this.details = [
        { id: 'TMDT-message', src1: 'processor.png', component: 'Processor', processor: '2.9GHz Quad-core Intel Core i5, Turbo Boost up to 3.6GHz', src2: 'memory.png', component2: 'Memory', memory: '4GB 1600MHz LPDDR3 SDRAM', src3: 'drive.png', component3: 'Hard Drive', drive: '500GB Serial ATA Drive @ 5400 rpm' }];

}


store.prototype.getProduct = function (code) {
    for (var i = 0; i < this.products.length; i++) {
        if (this.products[i].code === code)
            return this.products[i];
    }
    
    return null;
};
detailsprod.prototype.getDetail = function (code) {
    for (var i = 0; i < this.details.length; i++) {
        if (this.details[i].id === code)
            
            return this.details[i];
        
    }
    return null;
};
