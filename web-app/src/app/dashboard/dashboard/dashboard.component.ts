import {Component, OnInit, OnDestroy} from '@angular/core';
import {DashboardCard} from '../cards/dashboard-card';
import {Observable} from 'rxjs/Observable';
import {DashboardCardsService} from '../services/dashboard-cards/dashboard-cards.service';
import {ObservableMedia} from '@angular/flex-layout';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/startWith';
import {DashboardChatComponent} from '../cards/dashboard-chat/dashboard-chat.component';
import {DashboardPostsComponent} from "../cards/dashboard-posts/dashboard-posts.component";
import {DashboardFriendsComponent} from "../cards/dashboard-friends/dashboard-friends.component";
import {DashboardUsersComponent} from "../cards/dashboard-users/dashboard-users.component";

import {UsersComponentService} from "../cards/dashboard-users/dashboard-users.component.service";

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss'],
  entryComponents: [DashboardChatComponent, DashboardPostsComponent, DashboardFriendsComponent, DashboardUsersComponent]
})
export class DashboardComponent implements OnInit, OnDestroy {

  cards: DashboardCard[] = [];
  cols: Observable<number>;
  cols_big: Observable<number>;
  cols_sml: Observable<number>;
    cols_vbig: Observable<number>;
    cols_vvbig: Observable<number>;

  windowSize: {width: any, height: any}
  
  chatSize: any;
  friendsSize: any;
  usersSize: any;
  postsSize: any;

  config: any;

  constructor(private cardsService: DashboardCardsService,
              private observableMedia: ObservableMedia,
              private usersService: UsersComponentService) {
    this.cardsService.cards.subscribe(cards => {
      this.cards = cards;
    });
  }

  ngOnInit() {
      // this.cardsService.destroyCards();

      // this.usersService.getConfigAction().subscribe(data => {
      //     var jsonData = data.userDashboard.Dashboard.replace('/\"','/')
      //     this.config = JSON.parse(jsonData);
      //     console.log(JSON.stringify(this.config))
      //       this.chatSize = this.config.chat;
      //       this.postsSize = this.config.posts;
      //       this.usersSize = this.config.users;
      //       this.friendsSize = this.config.friends;
      //     // this.setSize(this.config.chat.hgt, this.config.chat.wdt, "chat");
      //     // this.setSize(this.config.posts.hgt, this.config.posts.wdt, "post");
      //     // this.setSize(this.config.users.hgt, this.config.users.wdt, "users");
      //     // this.setSize(this.config.friends.hgt, this.config.friends.wdt, "friends");
      //       console.log(this.chatSize)
      //     console.log(this.postsSize)
      //     console.log(this.usersSize)
      //     console.log(this.friendsSize)
      //
      // });
    /* Grid column map */
    const cols_map = new Map([
      ['xs', 1],
      ['sm', 4],
      ['md', 8],
      ['lg', 10],
      ['xl', 18]
    ]);
      /* Very very big card column span map */
      const cols_map_vvbig = new Map([
          ['xs', 1],
          ['sm', 5],
          ['md', 5],
          ['lg', 10],
          ['xl', 18]
      ]);

      /* Very big card column span map */
      const cols_map_vbig = new Map([
          ['xs', 1],
          ['sm', 5],
          ['md', 5],
          ['lg', 6],
          ['xl', 12]
      ]);

    /* Big card column span map */
    const cols_map_big = new Map([
      ['xs', 1],
      ['sm', 5],
      ['md', 5],
      ['lg', 5],
      ['xl', 9]
    ]);
    /* Small card column span map */
    const cols_map_sml = new Map([
      ['xs', 1],
      ['sm', 3],
      ['md', 3],
      ['lg', 3],
      ['xl', 5]
    ]);
    let start_cols: number;
    let start_cols_big: number;
    let start_cols_vbig: number;
    let start_cols_vvbig: number;
    let start_cols_sml: number;

    cols_map.forEach((cols, mqAlias) => {
      if (this.observableMedia.isActive(mqAlias)) {
        start_cols = cols;
      }
    });
    cols_map_big.forEach((cols_big, mqAlias) => {
      if (this.observableMedia.isActive(mqAlias)) {
        start_cols_big = cols_big;
      }
    });
      cols_map_vbig.forEach((cols_vbig, mqAlias) => {
          if (this.observableMedia.isActive(mqAlias)) {
              start_cols_vbig = cols_vbig;
          }
      });
      cols_map_vvbig.forEach((cols_vvbig, mqAlias) => {
          if (this.observableMedia.isActive(mqAlias)) {
              start_cols_vvbig = cols_vvbig;
          }
      });
    cols_map_sml.forEach((cols_sml, mqAliast) => {
      if (this.observableMedia.isActive(mqAliast)) {
        start_cols_sml = cols_sml;
      }
    });

    this.cols = this.observableMedia.asObservable()
      .map(change => {
        return cols_map.get(change.mqAlias);
      }).startWith(start_cols);

    this.cols_big = this.observableMedia.asObservable()
      .map(change => {
        return cols_map_big.get(change.mqAlias);
      }).startWith(start_cols_big);

      this.cols_vbig = this.observableMedia.asObservable()
          .map(change => {
              return cols_map_vbig.get(change.mqAlias);
          }).startWith(start_cols_vbig);

      this.cols_vvbig = this.observableMedia.asObservable()
          .map(change => {
              return cols_map_vvbig.get(change.mqAlias);
          }).startWith(start_cols_vvbig);

    this.cols_sml = this.observableMedia.asObservable()
      .map(change => {
        return cols_map_sml.get(change.mqAlias);
      }).startWith(start_cols_sml);

      this.usersService.getConfigAction().subscribe(data => {
          var jsonData = data.userDashboard.Dashboard.replace('/\"','/')
          this.config = JSON.parse(jsonData);
          console.log(JSON.stringify(this.config))
          this.chatSize = this.config.chat;
          this.postsSize = this.config.posts;
          this.usersSize = this.config.users;
          this.friendsSize = this.config.friends;
          // this.setSize(this.config.chat.hgt, this.config.chat.wdt, "chat");
          // this.setSize(this.config.posts.hgt, this.config.posts.wdt, "post");
          // this.setSize(this.config.users.hgt, this.config.users.wdt, "users");
          // this.setSize(this.config.friends.hgt, this.config.friends.wdt, "friends");
          console.log(this.chatSize)
          console.log(this.postsSize)
          console.log(this.usersSize)
          console.log(this.friendsSize)
          this.createCards();
      });
    // this.createCards();
  }

  ngOnDestroy(){
      this.cardsService.destroyCards();
  }

  setSize(height, width, variable) {
      var hgt;
      var wdt;

      console.log(variable)
      if(height == 2){
          hgt = this.cols_big;
      }else {
          hgt = this.cols_sml;
      }

      if(width == 2){
          wdt = this.cols_big;
      }else {
          wdt = this.cols_sml;
      }
      
      switch (variable) {
          case "chat":
              console.log("robie czat")
              this.chatSize = {width: wdt, height: hgt}
              // this.chatSize.height = hgt ;
              // this.chatSize.width = wdt;
              break;
          case "post":
              console.log("robie p")

              this.postsSize = {width: wdt, height: hgt}
              // this.postsSize.height = hgt;
              // this.postsSize.width = wdt;
              break;
          case "users":
              console.log("robie u")

              this.usersSize = {width: wdt, height: hgt}
              // this.usersSize.height = hgt;
              // this.usersSize.width = wdt;
              break;
          case "friends":
              console.log("robie f")

              this.friendsSize = {width: wdt, height: hgt}
              // this.friendsSize.height = hgt;
              // this.friendsSize.width = wdt;
              break;

          default:
              console.log(variable)
              alert("Wystąpił problem")
      }
      
  }
  
  createCards(): void {
      if(this.chatSize.wdt > 0 && this.chatSize.hgt > 0) {
          this.cardsService.addCard(
              new DashboardCard(
                  {
                      name: {
                          key: DashboardCard.metadata.NAME,
                          value: 'chat'
                      },
                      routerLink: {
                          key: DashboardCard.metadata.ROUTERLINK,
                          value: '/dashboard/users'
                      },
                      iconClass: {
                          key: DashboardCard.metadata.ICONCLASS,
                          value: 'fa-users'
                      },
                      cols: {
                          key: DashboardCard.metadata.COLS,
                          value: this.chatSize.wdt > 1 ? this.chatSize.wdt > 2 ? this.chatSize.wdt > 3 ? this.cols_vvbig : this.cols_vbig : this.cols_big : this.cols_sml

                      },
                      rows: {
                          key: DashboardCard.metadata.ROWS,
                          value: this.chatSize.hgt == 2 ? this.cols_big : this.cols_sml
                      },
                      color: {
                          key: DashboardCard.metadata.COLOR,
                          value: 'blue'
                      }
                  }, DashboardChatComponent /* Reference to the component we'd like to spawn */
              )
          );
      }
      if(this.postsSize.wdt > 0 && this.postsSize.hgt > 0) {

          this.cardsService.addCard(
              new DashboardCard(
                  {
                      name: {
                          key: DashboardCard.metadata.NAME,
                          value: 'posts'
                      },
                      routerLink: {
                          key: DashboardCard.metadata.ROUTERLINK,
                          value: '/dashboard/chat'
                      },
                      iconClass: {
                          key: DashboardCard.metadata.ICONCLASS,
                          value: 'fa-users'
                      },
                      cols: {
                          key: DashboardCard.metadata.COLS,
                          value: this.postsSize.wdt > 1 ? this.postsSize.wdt > 2 ? this.postsSize.wdt > 3 ? this.cols_vvbig : this.cols_vbig : this.cols_big : this.cols_sml

                      },
                      rows: {
                          key: DashboardCard.metadata.ROWS,
                          value: this.postsSize.hgt == 2 ? this.cols_big : this.cols_sml
                      },
                      color: {
                          key: DashboardCard.metadata.COLOR,
                          value: 'blue'
                      }
                  }, DashboardPostsComponent
              )
          );
      }
      if(this.friendsSize.wdt > 0 && this.friendsSize.hgt > 0) {
          this.cardsService.addCard(
              new DashboardCard(
                  {
                      name: {
                          key: DashboardCard.metadata.NAME,
                          value: 'users'
                      },
                      routerLink: {
                          key: DashboardCard.metadata.ROUTERLINK,
                          value: '/dashboard/posts'
                      },
                      iconClass: {
                          key: DashboardCard.metadata.ICONCLASS,
                          value: 'fa-users'
                      },
                      cols: {
                          key: DashboardCard.metadata.COLS,
                          value: this.friendsSize.wdt > 1 ? this.friendsSize.wdt > 2 ? this.friendsSize.wdt > 3 ? this.cols_vvbig : this.cols_vbig : this.cols_big : this.cols_sml

                      },
                      rows: {
                          key: DashboardCard.metadata.ROWS,
                          value: this.friendsSize.hgt == 2 ? this.cols_big : this.cols_sml
                      },
                      color: {
                          key: DashboardCard.metadata.COLOR,
                          value: 'blue'
                      }
                  }, DashboardFriendsComponent
              )
          );
      }
      if(this.usersSize.wdt > 0 && this.usersSize.hgt > 0) {
          this.cardsService.addCard(
              new DashboardCard(
                  {
                      name: {
                          key: DashboardCard.metadata.NAME,
                          value: 'users'
                      },
                      routerLink: {
                          key: DashboardCard.metadata.ROUTERLINK,
                          value: '/dashboard/users'
                      },
                      iconClass: {
                          key: DashboardCard.metadata.ICONCLASS,
                          value: 'fa-users'
                      },
                      cols: {
                          key: DashboardCard.metadata.COLS,
                          value: this.usersSize.wdt > 1 ? this.usersSize.wdt > 2 ? this.usersSize.wdt > 3 ? this.cols_vvbig : this.cols_vbig : this.cols_big : this.cols_sml

                      },
                      rows: {
                          key: DashboardCard.metadata.ROWS,
                          value: this.usersSize.hgt == 2 ? this.cols_big : this.cols_sml
                      },
                      color: {
                          key: DashboardCard.metadata.COLOR,
                          value: 'blue'
                      }
                  }, DashboardUsersComponent
              )
          );
      }

  }

}
