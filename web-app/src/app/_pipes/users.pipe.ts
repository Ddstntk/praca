import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'usersfilter',
    pure: false
})
export class UsersFilterPipe implements PipeTransform {
    transform(items: any[], filter: Object): any {
        if (!items || !filter) {
            return items;
        }
        // filter items array, items which match and return true will be
        // kept, false will be filtered out
        return items.filter(
            item => item.name.indexOf(filter) !== -1 ||
                item.surname.indexOf(filter) !== -1
        );
    }
}