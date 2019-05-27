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
  
  windowSize: {width: any, height: any}
  
  chatSize = this.windowSize;
  friendsSize = this.windowSize;
  usersSize = this.windowSize;
  postsSize = this.windowSize;
  
  constructor(private cardsService: DashboardCardsService,
              private observableMedia: ObservableMedia) {
    this.cardsService.cards.subscribe(cards => {
      this.cards = cards;
    });
  }

  ngOnInit() {
    /* Grid column map */
    const cols_map = new Map([
      ['xs', 1],
      ['sm', 4],
      ['md', 8],
      ['lg', 10],
      ['xl', 18]
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
    
    this.cols_sml = this.observableMedia.asObservable()
      .map(change => {
        return cols_map_sml.get(change.mqAlias);
      }).startWith(start_cols_sml);
    
    this.createCards();
  }

  ngOnDestroy(){
      this.cards = [];
  }

  setChatSize(height, width, variable) {
      var hgt;
      var wdt;
      
      if(height == "big"){
          hgt = this.cols_big;
      }else {
          hgt = this.cols_sml;
      }

      if(width == "big"){
          wdt = this.cols_big;
      }else {
          wdt = this.cols_sml;
      }
      
      switch (variable) {
          case "chat":
              this.chatSize.height = hgt ;
              this.chatSize.width = wdt;
              break;
          case "post":
              this.postsSize.height = hgt;
              this.postsSize.width = wdt;
              break;
          case "users":
              this.usersSize.height = hgt;
              this.usersSize.width = wdt;
              break;
          case "friends":
              this.friendsSize.height = hgt;
              this.friendsSize.width = wdt;
          default:
              alert("Wystąpił problem")
      }
      
  }
  
  createCards(): void {
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
            value: this.cols_big
          },
          rows: {
            key: DashboardCard.metadata.ROWS,
            value: this.cols_sml
          },
          color: {
            key: DashboardCard.metadata.COLOR,
            value: 'blue'
          }
        }, DashboardChatComponent /* Reference to the component we'd like to spawn */
      )
    );
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
            value: this.cols_big
          },
          rows: {
            key: DashboardCard.metadata.ROWS,
            value: this.cols_sml
          },
          color: {
            key: DashboardCard.metadata.COLOR,
            value: 'blue'
          }
        }, DashboardPostsComponent
      )
    );
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
                      value: this.cols_big
                  },
                  rows: {
                      key: DashboardCard.metadata.ROWS,
                      value: this.cols_sml
                  },
                  color: {
                      key: DashboardCard.metadata.COLOR,
                      value: 'blue'
                  }
              }, DashboardFriendsComponent
          )
      );
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
                      value: this.cols_big
                  },
                  rows: {
                      key: DashboardCard.metadata.ROWS,
                      value: this.cols_sml
                  },
                  color: {
                      key: DashboardCard.metadata.COLOR,
                      value: 'blue'
                  }
              }, DashboardUsersComponent
          )
      );
    // this.cardsService.addCard(
    //   new DashboardCard(
    //     {
    //       name: {
    //         key: DashboardCard.metadata.NAME,
    //         value: 'users'
    //       },
    //       routerLink: {
    //         key: DashboardCard.metadata.ROUTERLINK,
    //         value: '/dashboard/users'
    //       },
    //       iconClass: {
    //         key: DashboardCard.metadata.ICONCLASS,
    //         value: 'fa-users'
    //       },
    //       cols: {
    //         key: DashboardCard.metadata.COLS,
    //         value: this.cols_sml
    //       },
    //       rows: {
    //         key: DashboardCard.metadata.ROWS,
    //         value: this.cols_sml
    //       },
    //       color: {
    //         key: DashboardCard.metadata.COLOR,
    //         value: 'blue'
    //       }
    //     }, DashboardUsersComponent
    //   )
    // );
    // this.cardsService.addCard(
    //   new DashboardCard(
    //     {
    //       name: {
    //         key: DashboardCard.metadata.NAME,
    //         value: 'users'
    //       },
    //       routerLink: {
    //         key: DashboardCard.metadata.ROUTERLINK,
    //         value: '/dashboard/users'
    //       },
    //       iconClass: {
    //         key: DashboardCard.metadata.ICONCLASS,
    //         value: 'fa-users'
    //       },
    //       cols: {
    //         key: DashboardCard.metadata.COLS,
    //         value: this.cols_sml
    //       },
    //       rows: {
    //         key: DashboardCard.metadata.ROWS,
    //         value: this.cols_sml
    //       },
    //       color: {
    //         key: DashboardCard.metadata.COLOR,
    //         value: 'blue'
    //       }
    //     }, DashboardUsersComponent
    //   )
    // );
    // this.cardsService.addCard(
    //   new DashboardCard(
    //     {
    //       name: {
    //         key: DashboardCard.metadata.NAME,
    //         value: 'users'
    //       },
    //       routerLink: {
    //         key: DashboardCard.metadata.ROUTERLINK,
    //         value: '/dashboard/users'
    //       },
    //       iconClass: {
    //         key: DashboardCard.metadata.ICONCLASS,
    //         value: 'fa-users'
    //       },
    //       cols: {
    //         key: DashboardCard.metadata.COLS,
    //         value: this.cols_big
    //       },
    //       rows: {
    //         key: DashboardCard.metadata.ROWS,
    //         value: this.cols_sml
    //       },
    //       color: {
    //         key: DashboardCard.metadata.COLOR,
    //         value: 'blue'
    //       }
    //     }, DashboardUsersComponent
    //   )
    // );
    // this.cardsService.addCard(
    //   new DashboardCard(
    //     {
    //       name: {
    //         key: DashboardCard.metadata.NAME,
    //         value: 'users'
    //       },
    //       routerLink: {
    //         key: DashboardCard.metadata.ROUTERLINK,
    //         value: '/dashboard/users'
    //       },
    //       iconClass: {
    //         key: DashboardCard.metadata.ICONCLASS,
    //         value: 'fa-users'
    //       },
    //       cols: {
    //         key: DashboardCard.metadata.COLS,
    //         value: this.cols_big
    //       },
    //       rows: {
    //         key: DashboardCard.metadata.ROWS,
    //         value: this.cols_sml
    //       },
    //       color: {
    //         key: DashboardCard.metadata.COLOR,
    //         value: 'blue'
    //       }
    //     }, DashboardUsersComponent
    //   )
    // );
    // this.cardsService.addCard(
    //   new DashboardCard(
    //     {
    //       name: {
    //         key: DashboardCard.metadata.NAME,
    //         value: 'users'
    //       },
    //       routerLink: {
    //         key: DashboardCard.metadata.ROUTERLINK,
    //         value: '/dashboard/users'
    //       },
    //       iconClass: {
    //         key: DashboardCard.metadata.ICONCLASS,
    //         value: 'fa-users'
    //       },
    //       cols: {
    //         key: DashboardCard.metadata.COLS,
    //         value: this.cols_big
    //       },
    //       rows: {
    //         key: DashboardCard.metadata.ROWS,
    //         value: this.cols_sml
    //       },
    //       color: {
    //         key: DashboardCard.metadata.COLOR,
    //         value: 'blue'
    //       }
    //     }, DashboardUsersComponent
    //   )
    // );
    // this.cardsService.addCard(
    //   new DashboardCard(
    //     {
    //       name: {
    //         key: DashboardCard.metadata.NAME,
    //         value: 'users'
    //       },
    //       routerLink: {
    //         key: DashboardCard.metadata.ROUTERLINK,
    //         value: '/dashboard/users'
    //       },
    //       iconClass: {
    //         key: DashboardCard.metadata.ICONCLASS,
    //         value: 'fa-users'
    //       },
    //       cols: {
    //         key: DashboardCard.metadata.COLS,
    //         value: this.cols_sml
    //       },
    //       rows: {
    //         key: DashboardCard.metadata.ROWS,
    //         value: this.cols_sml
    //       },
    //       color: {
    //         key: DashboardCard.metadata.COLOR,
    //         value: 'blue'
    //       }
    //     }, DashboardUsersComponent
    //   )
    // );
    // this.cardsService.addCard(
    //   new DashboardCard(
    //     {
    //       name: {
    //         key: DashboardCard.metadata.NAME,
    //         value: 'users'
    //       },
    //       routerLink: {
    //         key: DashboardCard.metadata.ROUTERLINK,
    //         value: '/dashboard/users'
    //       },
    //       iconClass: {
    //         key: DashboardCard.metadata.ICONCLASS,
    //         value: 'fa-users'
    //       },
    //       cols: {
    //         key: DashboardCard.metadata.COLS,
    //         value: this.cols_sml
    //       },
    //       rows: {
    //         key: DashboardCard.metadata.ROWS,
    //         value: this.cols_sml
    //       },
    //       color: {
    //         key: DashboardCard.metadata.COLOR,
    //         value: 'blue'
    //       }
    //     }, DashboardUsersComponent
    //   )
    // );
    // this.cardsService.addCard(
    //   new DashboardCard(
    //     {
    //       name: {
    //         key: DashboardCard.metadata.NAME,
    //         value: 'users'
    //       },
    //       routerLink: {
    //         key: DashboardCard.metadata.ROUTERLINK,
    //         value: '/dashboard/users'
    //       },
    //       iconClass: {
    //         key: DashboardCard.metadata.ICONCLASS,
    //         value: 'fa-users'
    //       },
    //       cols: {
    //         key: DashboardCard.metadata.COLS,
    //         value: this.cols_sml
    //       },
    //       rows: {
    //         key: DashboardCard.metadata.ROWS,
    //         value: this.cols_sml
    //       },
    //       color: {
    //         key: DashboardCard.metadata.COLOR,
    //         value: 'blue'
    //       }
    //     }, DashboardUsersComponent
    //   )
    // );
    // this.cardsService.addCard(
    //   new DashboardCard(
    //     {
    //       name: {
    //         key: DashboardCard.metadata.NAME,
    //         value: 'users'
    //       },
    //       routerLink: {
    //         key: DashboardCard.metadata.ROUTERLINK,
    //         value: '/dashboard/users'
    //       },
    //       iconClass: {
    //         key: DashboardCard.metadata.ICONCLASS,
    //         value: 'fa-users'
    //       },
    //       cols: {
    //         key: DashboardCard.metadata.COLS,
    //         value: this.cols_sml
    //       },
    //       rows: {
    //         key: DashboardCard.metadata.ROWS,
    //         value: this.cols_sml
    //       },
    //       color: {
    //         key: DashboardCard.metadata.COLOR,
    //         value: 'blue'
    //       }
    //     }, DashboardUsersComponent
    //   )
    // );
    // this.cardsService.addCard(
    //   new DashboardCard(
    //     {
    //       name: {
    //         key: DashboardCard.metadata.NAME,
    //         value: 'users'
    //       },
    //       routerLink: {
    //         key: DashboardCard.metadata.ROUTERLINK,
    //         value: '/dashboard/users'
    //       },
    //       iconClass: {
    //         key: DashboardCard.metadata.ICONCLASS,
    //         value: 'fa-users'
    //       },
    //       cols: {
    //         key: DashboardCard.metadata.COLS,
    //         value: this.cols_sml
    //       },
    //       rows: {
    //         key: DashboardCard.metadata.ROWS,
    //         value: this.cols_sml
    //       },
    //       color: {
    //         key: DashboardCard.metadata.COLOR,
    //         value: 'blue'
    //       }
    //     }, DashboardUsersComponent
    //   )
    // );
    // this.cardsService.addCard(
    //   new DashboardCard(
    //     {
    //       name: {
    //         key: DashboardCard.metadata.NAME,
    //         value: 'users'
    //       },
    //       routerLink: {
    //         key: DashboardCard.metadata.ROUTERLINK,
    //         value: '/dashboard/users'
    //       },
    //       iconClass: {
    //         key: DashboardCard.metadata.ICONCLASS,
    //         value: 'fa-users'
    //       },
    //       cols: {
    //         key: DashboardCard.metadata.COLS,
    //         value: this.cols_sml
    //       },
    //       rows: {
    //         key: DashboardCard.metadata.ROWS,
    //         value: this.cols_sml
    //       },
    //       color: {
    //         key: DashboardCard.metadata.COLOR,
    //         value: 'blue'
    //       }
    //     }, DashboardUsersComponent
    //   )
    // );
  }

}
