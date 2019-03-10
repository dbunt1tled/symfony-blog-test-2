import {BLOG_POST_LIST, BLOG_POST_LIST_ADD, BLOG_POST_LIST_ERROR, BLOG_POST_LIST_RECEIVED} from "../actions/actions";

export default (state = {
    post: null,
    isFetching: false
}, action) => {
    switch (action.type) {
        case BLOG_POST_LIST:
            state = {
                ...state,
                isFetching: true,
            };
            return state
        case BLOG_POST_LIST_RECEIVED:
            state = {
                ...state,
                posts: action.data['hydra:member'],
                isFetching: false
            }
            return state
        case BLOG_POST_LIST_ERROR:
            state = {
                ...state,
                isFetching: true,
                posts: null
            }
            return state
        case BLOG_POST_LIST_ADD:
            state = {
                ...state,
                posts: state.posts ? state.posts.concat(action.data) : state.posts
            }
            return state
        default:
            return state;
    }
    
    
    return state;
};