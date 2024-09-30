import React, { useCallback, useEffect, useRef, useState,useContext } from "react";
import {
    Page,
    Button,
    BlockStack,
    Popover,
    ActionList,
    Icon,
    OptionList,
    Card,
    Text,
    InlineStack,
    EmptySearchResult,
    Toast,
    IndexTable,
    LegacyStack,
    Badge,
    Box,
    RangeSlider,
    IndexFilters,
    useSetIndexFiltersMode,
    IndexFiltersMode,
    ChoiceList,
    TextField,
    useIndexResourceState,
    Pagination,
    Grid,
    InlineGrid,
    Select,
    Scrollable,
    DatePicker,
    useBreakpoints,
    ButtonGroup,
    Layout, Loading,
} from "@shopify/polaris";
import {
  MinusCircleIcon,
  CalendarIcon,
  DeleteIcon,
  DuplicateIcon,
  MenuHorizontalIcon,
  CheckSmallIcon,
  ArrowRightIcon,
  MegaphoneIcon,
  ReceiptRefundIcon,
  OrderIcon,
  DeliveryIcon,
  OrderFulfilledIcon,
  OrderUnfulfilledIcon,
  OrderDraftIcon,
  EditIcon,
} from "@shopify/polaris-icons";
import { useNavigate } from "react-router-dom";
import {getSessionToken} from "@shopify/app-bridge-utils";

import axios from "axios";
import {AppContext} from "../../components/index.js";
import {useAppBridge} from "@shopify/app-bridge-react";
import SkeletonTable from "../../components/SkeletonTable.jsx";

export default function Campaigns() {
  const navigate = useNavigate();
    const [loading, setLoading] = useState(true)
    const { apiUrl } = useContext(AppContext);
    const appBridge = useAppBridge();
    const [orderCreate, setOrderCreate] = useState(1);
    const [orderUpdate, setOrderUpdate] = useState(1);
    const [orderCancel, setOrderCancel] = useState(1);
    const [orderFulfill, setOrderFulfill] = useState(1);
    const [toastMsg, setToastMsg] = useState('')
    const [errorToast, setErrorToast] = useState(false);
    const [sucessToast, setSucessToast] = useState(false);


    // ------------------------Toasts Code start here------------------
    const toggleErrorMsgActive = useCallback(() => setErrorToast((errorToast) => !errorToast), []);
    const toggleSuccessMsgActive = useCallback(() => setSucessToast((sucessToast) => !sucessToast), []);


    const toastErrorMsg = errorToast ? (
        <Toast content={toastMsg} error onDismiss={toggleErrorMsgActive} />
    ) : null;

    const toastSuccessMsg = sucessToast ? (
        <Toast content={toastMsg} onDismiss={toggleSuccessMsgActive} />
    ) : null;



    const fetchData = async () => {
setLoading(true)
        try {
            let sessionToken = await getSessionToken(appBridge);

            const response = await axios.get(
                `${apiUrl}campaigns`,
                {
                    headers: {
                        Authorization: `Bearer ${sessionToken}`,
                    },
                }
            );

            if (response?.status === 200) {
                setOrderCreate(response?.data?.data?.order_create_enable);
                setOrderUpdate(response?.data?.data?.order_update_enable);
                setOrderCancel(response?.data?.data?.order_cancel_enable);
                setOrderFulfill(response?.data?.data?.order_fulfill_enable);

                setLoading(false);
            }
        } catch (error) {
            console.error("Error fetching data:", error);
        } finally {
            setLoading(false);


        }
    };


    useEffect(() => {
        fetchData();
    }, []);

    const handleCheckboxChangeIsActive = async (smsType, value) => {

        switch (smsType) {
            case 'order_create':
                setOrderCreate(value);
                break;
            case 'order_fulfill':
                setOrderFulfill(value);
                break;
            case 'order_cancel':
                setOrderCancel(value);
                break;
            case 'order_update':
                setOrderUpdate(value);
                break;
            default:
                break;
        }

        try {
            let sessionToken = await getSessionToken(appBridge);

            const response = await axios.get(
                `${apiUrl}update-campaign-status?status=${smsType}&value=${value}`,
                {
                    headers: {
                        Authorization: `Bearer ${sessionToken}`,
                    },
                }
            );

            if (response?.status === 200) {

                setSucessToast(true);
                setToastMsg(response?.data?.message);

                setLoading(false);
            }
        } catch (error) {
            console.error("Error fetching data:", error);
        } finally {
            setLoading(false);


        }
    };

  return (
    <Page title="Campaigns">
        {loading ?
            <span>
                    <Loading/>
                       <SkeletonTable/>
                </span>
            :
            <Layout>
                <Layout.Section>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div className="flex [&_.Polaris-Box]:flex w-full">
                            <Card>
                                <div className="flex flex-col space-y-2">
                                    <div className="flex flex-col space-y-2">
                                        <InlineStack align="space-between" blockAlign="center">
                                            <Text as="h2" variant="headingMd">
                                                Order created
                                            </Text>
                                            <Button icon={EditIcon} onClick={() => navigate("/campaigns/1")}/>
                                        </InlineStack>
                                        <Text variant="bodyMd" tone="subdued">
                                            Engage your audience by sending instant and personalized welcome messages,
                                            creating a positive and inviting experience. PS:
                                            Don't forget to add a little promotion to encourage your clients to make a
                                            purchase!
                                        </Text>
                                    </div>
                                    <div className="flex flex-1 flex-col justify-end">
                                        <InlineStack align="start" blockAlign="center">
                      <span>
                        <input
                            id="toggle-order-create"
                            type="checkbox"
                            name="status"
                            className="tgl tgl-light"
                            checked={orderCreate}
                            onChange={(e) => handleCheckboxChangeIsActive('order_create', e.target.checked ? 1 : 0)}
                        />
                         <label htmlFor="toggle-order-create" className="tgl-btn"></label>
                      </span>
                                        </InlineStack>
                                    </div>
                                </div>
                            </Card>
                        </div>
                        <div className="flex [&_.Polaris-Box]:flex w-full">
                            <Card>
                                <div className="flex flex-col space-y-2">
                                    <div className="flex flex-col space-y-2">
                                        <InlineStack align="space-between" blockAlign="center">
                                            <Text as="h2" variant="headingMd">
                                                Order fulfilled
                                            </Text>
                                            <Button icon={EditIcon} onClick={() => navigate("/campaigns/2")}/>
                                        </InlineStack>
                                        <Text variant="bodyMd" tone="subdued">
                                            Keep your clients informed effortlessly about processed refunds, ensuring a
                                            smooth and transparent financial interaction.
                                        </Text>
                                    </div>
                                    <div className="flex flex-1 flex-col justify-end">
                                        <InlineStack align="start" blockAlign="end">
                      <span>
                        <input
                            id="toggle-order-fulfill"
                            type="checkbox"
                            name="status"
                            className="tgl tgl-light"
                            checked={orderFulfill}
                            onChange={(e) => handleCheckboxChangeIsActive('order_fulfill', e.target.checked ? 1 : 0)}
                        />
                       <label htmlFor="toggle-order-fulfill" className="tgl-btn"></label>
                      </span>
                                        </InlineStack>
                                    </div>
                                </div>
                            </Card>
                        </div>
                        <div className="flex [&_.Polaris-Box]:flex w-full">
                            <Card>
                                <div className="flex flex-col space-y-2">
                                    <div className="flex flex-col space-y-2">
                                        <InlineStack align="space-between" blockAlign="center">
                                            <Text as="h2" variant="headingMd">
                                                Order cancelled
                                            </Text>
                                            <Button icon={EditIcon} onClick={() => navigate("/campaigns/3")}/>
                                        </InlineStack>
                                        <Text variant="bodyMd" tone="subdued">
                                            Keep your customers in the loop by automatically sending shipping
                                            confirmations. Enhance customer satisfaction with timely
                                            updates on their order shipments.
                                        </Text>
                                    </div>
                                    <div className="flex flex-1 flex-col justify-end">
                                        <InlineStack align="start" blockAlign="end">
                      <span>
                        <input
                            id="toggle-order-cancel"
                            type="checkbox"
                            name="status"
                            className="tgl tgl-light"
                            checked={orderCancel}
                            onChange={(e) => handleCheckboxChangeIsActive('order_cancel', e.target.checked ? 1 : 0)}
                        />
                       <label htmlFor="toggle-order-cancel" className="tgl-btn"></label>
                      </span>
                                        </InlineStack>
                                    </div>
                                </div>
                            </Card>
                        </div>
                        <div className="flex [&_.Polaris-Box]:flex w-full">
                            <Card>
                                <div className="flex flex-col space-y-2">
                                    <div className="flex flex-col space-y-2">
                                        <InlineStack align="space-between" blockAlign="center">
                                            <Text as="h2" variant="headingMd">
                                                Order updated
                                            </Text>
                                            <Button icon={EditIcon} onClick={() => navigate("/campaigns/4")}/>
                                        </InlineStack>
                                        <Text variant="bodyMd" tone="subdued">
                                            Keep your customers in the loop by automatically sending shipping
                                            confirmations. Enhance customer satisfaction with timely
                                            updates on their order shipments.{" "}
                                        </Text>
                                    </div>
                                    <div className="flex flex-1 flex-col justify-end">
                                        <InlineStack align="start" blockAlign="end">
                      <span>
                        <input
                            id="toggle-order-update"
                            type="checkbox"
                            name="status"
                            className="tgl tgl-light"
                            checked={orderUpdate}
                            onChange={(e) => handleCheckboxChangeIsActive('order_update', e.target.checked ? 1 : 0)}
                        />
                      <label htmlFor="toggle-order-update" className="tgl-btn"></label>
                      </span>
                                        </InlineStack>
                                    </div>
                                </div>
                            </Card>
                        </div>
                        {/* <div className="flex [&_.Polaris-Box]:flex">
              <Card>
                <div className="flex flex-col space-y-2">
                  <div className="flex flex-col space-y-1">
                    <InlineStack gap={"200"}>
                      <Text as="h2" variant="headingMd">
                        Order updated
                      </Text>
                    </InlineStack>
                    <Text variant="bodyMd" tone="subdued">
                      Keep your customers in the loop by automatically sending shipping confirmations. Enhance customer satisfaction with timely
                      updates on their order shipments.{" "}
                    </Text>
                    <InlineStack gap={"200"} blockAlign="center">
                      <Badge icon={OrderIcon}>Order</Badge>
                      <Badge icon={OrderDraftIcon}>Updated</Badge>
                    </InlineStack>
                  </div>
                  <div className="flex flex-1 flex-col justify-end">
                    <BlockStack gap={"200"}>
                      <div className="rounded-r-xl p-3 rounded-t-xl border bg-stone-200">
                        <div>
                          <p>Hi Richard, your order #8002 has been fulfilled.</p>
                          <br />
                          <p>
                            You can track your shipment here:{" "}
                            <a href="https://ysms.me/_s/jqq0asO" target="_blank" class="Polaris-Link">
                              https://ysms.me/s/jqq0asO
                            </a>
                            .
                          </p>
                        </div>
                      </div>
                      <InlineStack align="end" blockAlign="end">
                        <span>
                          <input
                            id={`toggle-${1}`}
                            type="checkbox"
                            name="status"
                            className="tgl tgl-light"
                            checked={true}
                            // onChange={() => handleCheckboxChangeIsActive(item?.id, item?.active_status)}
                          />
                          <label htmlFor={`toggle-${1}`} className="tgl-btn"></label>
                        </span>
                      </InlineStack>
                    </BlockStack>
                  </div>
                </div>
              </Card>
            </div> */}
                    </div>
                </Layout.Section>
                <Layout.Section></Layout.Section>
                <Layout.Section></Layout.Section>
            </Layout>
        }
        {toastErrorMsg}
        {toastSuccessMsg}

    </Page>
  );
}
