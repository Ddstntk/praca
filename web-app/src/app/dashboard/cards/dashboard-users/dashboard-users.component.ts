import {Component, Injector, OnInit} from '@angular/core';
import {DashboardCard} from '../dashboard-card';
import {AbstractDashboardCard} from '../abstract-dashboard-card';
import {UsersComponentService} from "./dashboard-users.component.service";
// import {ChatComponentService} from "../../../chat/chat.component.service";
// import {UsersComponentService} from "../../../users/users.component.service";
// import {PostsComponentService} from "./dashboard-posts.component.service";


@Component({
  selector: 'app-dashboard-users',
  templateUrl: './dashboard-users.component.html',
  styleUrls: ['./dashboard-users.component.scss']
})
export class DashboardUsersComponent extends AbstractDashboardCard implements OnInit {

  constructor(private injector: Injector, private usersService: UsersComponentService) {
    super(injector.get(DashboardCard.metadata.NAME),
      injector.get(DashboardCard.metadata.ROUTERLINK),
      injector.get(DashboardCard.metadata.ICONCLASS),
      injector.get(DashboardCard.metadata.COLS),
      injector.get(DashboardCard.metadata.ROWS),
      injector.get(DashboardCard.metadata.COLOR));
  }
  users: any;
  pagesNumber: number;
  filter: any;
  page: number;
  prevActive: any;
  nextActive: any;

  ngOnInit() {
    this.indexAction();
    this.page=1;
    // this.friends = this.friendsService.indexAction();
    // alert(JSON.stringify(this.friends));
  }

  indexAction(filter = false) {
    this.usersService.indexAction(filter, this.page).subscribe(data => {
      this.users = data.usersIndexed.data;
      this.pagesNumber = data.usersIndexed.pages_number
      if(this.page==1){
        this.prevActive = false;
      } else {
        this.prevActive = true;
      }
      if(this.page == this.pagesNumber) {
        this.nextActive = false;
      } else {
        this.nextActive = true;
      }
    });
  }

  getFiltered() {
    this.indexAction(this.filter);
    console.log(this.users)
  }

  inviteAction(id) {
    this.usersService.inviteAction(id).subscribe(data => {
    });
  }

  pageAction(value) {
    this.page += value;
    this.indexAction()
  }

}